<?php

namespace App\Conversations;

use App\Models\Resort;
use App\Services\Api\AirBnb;
use App\Services\Api\Booking;
use App\Services\Api\SkyScanner;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Web\WebDriver;
use Carbon\Carbon;

class ChooseResortConversation extends Conversation
{
    use AiChatTrait;

    protected $name;
    protected $budget;
    protected $level;
    protected $character;
    protected $duration;
    protected $date;
    protected $airportFrom;
    protected $airportTo;
    protected $resort = '';

    const TIMEOUT = 1;

    public function __construct(
        public ?string $initialMessage,
        public string $driver,
        public bool $shouldQueue
    ) {

    }

    public function run()
    {
        $this->askName();
    }

    protected function askName()
    {
        return $this->askInChat('The mountains are calling and you know it! Let’s get you there ASAP. I’m Matt, I can help you get your trip shredded. What’s your name, my friend?', function (Answer $answer) {
            $this->handleAnswer($answer);
            $this->name = AiCompletion::create('Get the name from this text, only return the name and nothing else. If they say no then return anonymous:' . $answer);
            $this->bot->typesAndWaits(self::TIMEOUT);

            $this->askBudget();
        });
    }

    protected function askBudget()
    {
        $this->sayInChat('Awesome. Now tell me a little bit about what you want from your trip, I’ll throw some questions at you, quick fire… don’t overthink them, go with your gut.');

        $options = [
            'I want to throw money' => 'high',
            'Cheap as chips' => 'low',
        ];

        $buttons = [];
        foreach ($options as $key => $option) {
            $buttons[] = Button::create($key)->value($option);
        }

        $question = Question::create('What’s your budget look like?')
            ->fallback('Unable to ask budget')
            ->callbackId('budget')
            ->addButtons($buttons);

        $this->askInChat($question, function (Answer $answer) use ($options) {
            if ($answer->isInteractiveMessageReply()) {
                $this->handleAnswer($answer);

                $text = array_search($answer->getValue(), $options);
                $this->sayInChat($text);

                $this->budget = $answer->getValue();
                $this->bot->typesAndWaits(self::TIMEOUT);

                $this->askLevel();
            }
        });
    }

    protected function askDuration()
    {
        $options = [
            '1-2 days',
            '3-5 days',
            '1 week+',
        ];

        $buttons = [];
        foreach ($options as $option) {
            $buttons[] = Button::create($option)->value($option);
        }

        $question = Question::create('How long can you get away for?')
            ->fallback('Unable to ask duration')
            ->callbackId('duration')
            ->addButtons($buttons);

        $this->askInChat($question, function (Answer $answer) use ($options) {
            if ($answer->isInteractiveMessageReply()) {
                $this->handleAnswer($answer);
                $this->sayInChat($answer->getValue());
                $this->duration = $answer->getValue();

                $this->bot->typesAndWaits(self::TIMEOUT);

                $this->showOffer();
            }
        });
    }

    protected function prepareOffer()
    {
        $resorts = Resort::pluck('name')->implode(', ');
        $prompt = 'Find me the best ski resort from the list: %s, which is perfect for a %s trip. I am a %s skier. The resort must be %s. Budget is %s. Only return one resort and a short overview about that resort.';
        $offer = AiCompletion::create(sprintf($prompt, $resorts, $this->duration, $this->level, $this->character, $this->budget));
        $this->resort = AiCompletion::create('Get the name of resort from this text: ' . $offer);

        return $offer;
    }

    protected function showOffer()
    {
        $offer = $this->prepareOffer();

        $this->sayInChat($offer);

        $question = Question::create('Shall we head here?')
            ->fallback('Unable to ask about offer')
            ->callbackId('offer')
            ->addButtons([
                Button::create('Yes')->value('yes'),
                Button::create('No')->value('no'),
            ]);

        $this->askInChat($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->handleAnswer($answer);

                $this->bot->typesAndWaits(self::TIMEOUT);

                if ($answer->getValue() == 'yes') {
                    $this->sayInChat($this->resort);
                    $this->askDate();
                } else {
                    $this->askBudget();
                }
            }
        });
    }

    protected function askDate()
    {
        return $this->askInChat('When can you go?', function (Answer $answer) {
            $this->handleAnswer($answer);
            $this->date = AiCompletion::create('Extract date from answer. If there is no year, then use current year 2024. Format date dd/mm/yyyy. If date not clear return null: ' . $answer);

            if ('null' == strtolower($this->date)) {
                $this->sayInChat('Sorry not clear. Could you repeat please?');
                $this->askDate();
            } else {
                // $this->sayInChat($this->date);
                $this->bot->typesAndWaits(self::TIMEOUT);
                $this->askAirport();
            }
        });
    }

    protected function askAirport()
    {
        return $this->askInChat('What’s the best UK airport for you to fly from?', function (Answer $answer) {
            $this->handleAnswer($answer);
            $this->airportFrom = AiCompletion::create('Get the code of airport from answer. If information not clear or airport not exists return null: ' . $answer);

            if ('null' == strtolower($this->airportFrom)) {
                $this->sayInChat('Sorry not clear. Could you repeat please? Maybe airport not exist');
                $this->askAirport();
            } else {
                $this->prepareFlightLink();
            }
        });
    }

    protected function prepareFlightLink()
    {
        $this->airportTo = AiCompletion::create('Get the code of closest airport to the resort from list: Geneva, Grenoble, Lyon St Exupery. Only code of the airport: ' . $this->resort);

        $this->sayInChat(
            sprintf('So the best place for flights from %s to %s is SkyScanner, click this link to find the best flight for you: <a target="_blank" href="%s">click here</a>',
                $this->airportFrom,
                $this->airportTo,
                SkyScanner::getLink($this->airportFrom, $this->airportTo, $this->date)
            )
        );

        $this->bot->typesAndWaits(3);
        $this->prepareTransfers();
    }

    protected function prepareHostels()
    {
        $this->sayInChat(
            sprintf('So, we’ve got you to resort, where are you going to stay? Check out <a target="_blank" href="%s">AirBnB</a> and <a target="_blank" href="%s">Booking.com</a> for your dates.',
                AirBnb::getLink($this->resort, $this->date),
                Booking::getLink($this->resort, $this->date)
            )
        );

        $this->bot->typesAndWaits(3);
        $this->prepareWeatherConditions();
    }

    protected function prepareWeatherConditions()
    {
        $question = Question::create('I think you are good to go… but shall we check out the weather in resort, is the white stuff aplenty yet? Here’s a lowdown of how it looks for next few days.')
            ->fallback('Unable to ask about weather')
            ->callbackId('weather')
            ->addButtons([
                Button::create('Yes')->value('yes'),
                Button::create('No')->value('no'),
            ]);

        $this->askInChat($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->handleAnswer($answer);
                if ($answer->getValue() == 'yes') {
                    $this->bot->typesAndWaits(self::TIMEOUT);
                    $summary = AiChat::start('Create summary for weather for resort: ' . $this->resort);
                    $summary = json_decode($summary, true);

                    $this->sayInChat(
                        nl2br($summary['content']['content'])
                    );

                    $this->bot->typesAndWaits(self::TIMEOUT);
                    $this->prepareSnowConditions();
                } else {
                    $this->askBudget();
                }
            }
        });
    }

    protected function prepareSnowConditions()
    {
        $question = Question::create('Shall we check out the snow conditions in resort?')
            ->fallback('Unable to ask about snow')
            ->callbackId('snow')
            ->addButtons([
                Button::create('Yes')->value('yes'),
                Button::create('No')->value('no'),
            ]);

        $this->askInChat($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->handleAnswer($answer);
                if ($answer->getValue() == 'yes') {
                    $this->bot->typesAndWaits(self::TIMEOUT);
                    $summary = AiChat::start('Create summary for snow conditions for resort: ' . $this->resort);
                    $summary = json_decode($summary, true);

                    $this->sayInChat(
                        nl2br($summary['content']['content'])
                    );

                    $this->bot->typesAndWaits(self::TIMEOUT);
                } else {
                    $this->askBudget();
                }
            }
        });
    }
}
