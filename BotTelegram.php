<?php

class BotTelegram
{
    private string $token;
    private string $stateDir;
    private ?int $chatId = null;
    private string $text = '';
    private array $state = ['access' => false];

    public function __construct(string $token, string $stateDir)
    {
        $this->token = $token;
        $this->stateDir = $stateDir;

        if (!file_exists($this->stateDir)) {
            mkdir($this->stateDir, 0777, true);
        }

        $this->loadUpdate();
        if ($this->chatId) {
            $this->loadState();
        }
    }

    private function loadUpdate(): void
    {
        $update = json_decode(file_get_contents('php://input'), true);
        $this->chatId = $update['message']['chat']['id'] ?? null;
        $this->text = trim($update['message']['text'] ?? '');
    }

    private function getStateFile(): string
    {
        return $this->stateDir . '/' . $this->chatId . '.json';
    }

    private function loadState(): void
    {
        $stateFile = $this->getStateFile();
        if (file_exists($stateFile)) {
            $this->state = json_decode(file_get_contents($stateFile), true);
        }
    }

    private function saveState(): void
    {
        file_put_contents($this->getStateFile(), json_encode($this->state));
    }

    private function sendMessage(string $text): void
    {
        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";
        $data = [
            'chat_id' => $this->chatId,
            'text' => $text,
        ];

        $options = [
            'http' => [
                'header'  => "Content-Type:application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
            ],
        ];
        $context = stream_context_create($options);
        file_get_contents($url, false, $context);
    }

    private function generateExample(): array
    {
        $a = rand(1, 99);
        $b = rand(1, 99);
        return [
            'example' => "$a + $b",
            'answer' => $a + $b,
        ];
    }

    public function handle(): void
    {
        if (!$this->chatId) {
            exit;
        }

        if ($this->state['access']) {
            $this->sendMessage("Фиксики существуют!");
            exit;
        }

        if (!isset($this->state['example'])) {
            $example = $this->generateExample();
            $this->state['example'] = $example['example'];
            $this->state['answer'] = $example['answer'];
            $this->saveState();
            $this->sendMessage("Привет, чтобы получить доступ к секретной информации, реши пример:\n\n" . $this->state['example']);
            exit;
        }

        if (is_numeric($this->text)) {
            if ((int)$this->text === $this->state['answer']) {
                $this->state['access'] = true;
                $this->saveState();
                $this->sendMessage("Отлично! Ты решил пример правильно.\n\nДоступ к секретной информации открыт!");
            } else {
                $this->sendMessage("Неправильно, попробуй ещё раз:\n\n" . $this->state['example']);
            }
            exit;
        }

        $this->sendMessage("Привет! Чтобы получить секрет, реши пример:\n\n" . $this->state['example']);
    }
}