<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ParseProxies extends Command
{
    protected $signature = 'parse:proxies
        {--type= : Формат полей: ip | ip,port | ip,port,login,password}
        {--format=object : Формат вывода: object | string}
        {--input_file= : Путь к файлу (ip:port:login:password)}
        {--output_file= : Путь к файлу для сохранения JSON (например: storage/output/proxies.json)}';

    protected $description = 'Парсит строки ip:port:login:password и сохраняет JSON в разных форматах';

    public function handle()
    {
        $inputFile = $this->option('input_file');
        $outputFile = $this->option('output_file');
        $type = $this->option('type') ?? 'ip,port,login,password';
        $format = $this->option('format') ?? 'object';

        if (!$inputFile || !file_exists($inputFile)) {
            $this->error('Файл не найден или не указан. Используйте --input_file=путь_к_файлу');
            return Command::FAILURE;
        }

        $lines = file($inputFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $parsed = [];

        foreach ($lines as $line) {
            $parts = array_map('trim', explode(':', $line));

            if (count($parts) < 2) {
                continue;
            }

            $entry = [
                'ip'       => $parts[0],
                'port'     => $parts[1],
                'login'    => $parts[2] ?? null,
                'password' => $parts[3] ?? null,
            ];

            switch ($type) {
                case 'ip':
                    $parsed[] = $format === 'string'
                        ? $entry['ip']
                        : ['ip' => $entry['ip']];
                    break;

                case 'ip,port':
                    $parsed[] = $format === 'string'
                        ? "{$entry['ip']}:{$entry['port']}"
                        : ['ip' => $entry['ip'], 'port' => $entry['port']];
                    break;

                case 'ip,port,login,password':
                default:
                    $parsed[] = $format === 'string'
                        ? "{$entry['ip']}:{$entry['port']}:{$entry['login']}:{$entry['password']}"
                        : $entry;
                    break;
            }
        }

        $json = json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if ($outputFile) {
            file_put_contents(base_path($outputFile), $json);
            $this->info("Результат сохранён в файл: $outputFile");
        } else {
            $this->line($json);
        }

        return Command::SUCCESS;
    }
}
