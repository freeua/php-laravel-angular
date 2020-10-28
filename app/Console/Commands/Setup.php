<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;

class Setup extends Command
{
    protected $environments = [
        'local',
        'development',
        'sandbox',
        'production'
    ];

    protected $logChannels = [
        'development',
        'sandbox',
        'production'
    ];

    protected $isFirstInstall;

    protected $domain;

    /* @var $environmentFile \DotenvEditor */
    protected $environmentFile;

    protected $defaultConfig;

    protected $envPath = __DIR__ . '/../../../.env';
    protected $exampleEnvPath = __DIR__ . '/../../../.env.example';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mercator:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the system';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $this->info('## Setup information ##');

        $this->copyEnvExample();

        $this->environmentFile = \DotenvEditor::load($this->envPath);
        $this->setupEnvironments();
        $app = require_once __DIR__.'/../../../bootstrap/app.php';
        $this->setLaravel($app);
        $this->setupDatabaseData();
        $this->setupSmtpEmail();
        $this->environmentFile->save();


        $this->runFreshMigrations();
        if ($this->confirm('Do you want to create a new system admin user', $this->isFirstInstall)) {
            $this->info('## Creating system admin user ##');
            $this->setupFirstSystemUser();
        }
    }


    public function copyEnvExample()
    {
        if (!file_exists($this->envPath)) {
            if (!copy($this->exampleEnvPath, $this->envPath)) {
                throw new \Error('File env example not copied');
            }
            $this->isFirstInstall = true;
        } else {
            $this->isFirstInstall = false;
        }
    }

    public function setupEnvironments()
    {
        $this->domain = $this->ask(
            'What is the parent domain of this setup? (without https://)',
            $this->environmentFile->getValue('APP_URL_BASE')
        );
        $this->environmentFile->setKey('APP_URL_BASE', $this->domain);
        $systemAdminDomain = $this->ask(
            'What domain is the system admin frontend? (without https://)',
            $this->environmentFile->getValue('SYSTEM_ADMIN_DOMAIN')
        );
        $this->environmentFile->setKey('SYSTEM_ADMIN_DOMAIN', $systemAdminDomain);

        if (!$this->environmentFile->getValue('SYSTEM_ADMIN_APP_KEY')) {
            $this->environmentFile->setKey('SYSTEM_ADMIN_APP_KEY', $this->generateKey());
        }

        $defaultIndexEnvironment = $this->getDefaultIndexEnvironment();

        $environment = $this->choice('Which environment are you performing this setup?', [
            'local',
            'development',
            'sandbox',
            'production'
        ], $defaultIndexEnvironment);

        $this->environmentFile->setKey('APP_ENV', $environment);
        if (!$this->environmentFile->getValue('APP_KEY')) {
            $this->environmentFile->setKey('APP_KEY', $this->generateKey());
        }

        $defaultIndexLogChannel = $this->getDefaultIndexLogChannel();

        $logChannel = $this->choice(
            'Which log channel do you want to configure?',
            $this->logChannels,
            $defaultIndexLogChannel
        );
        $this->environmentFile->setKey('LOG_CHANNEL', $logChannel);
    }

    public function setupDatabaseData()
    {
        $dbConnection = $this->choice(
            'What database connection do you want for this setup?',
            ['mysql'],
            0
        );
        $dbHost = $this->ask(
            'To which host is the database connected?',
            $this->environmentFile->getValue('DB_HOST')
        );
        $dbPort = $this->ask(
            'To which port is the database connected?',
            $this->environmentFile->getValue('DB_PORT')
        );
        $dbSchema = $this->ask(
            'To which schema is the database connected?',
            $this->environmentFile->getValue('DB_DATABASE')
        );
        $dbUser = $this->ask(
            'With which user is the database connected?',
            $this->environmentFile->getValue('DB_USERNAME')
        );
        $dbPass = $this->ask(
            'With which password is the database connected?',
            $this->environmentFile->getValue('DB_PASSWORD')
        );
        $this->environmentFile->setKey('DB_CONNECTION', $dbConnection);
        $this->environmentFile->setKey('DB_HOST', $dbHost);
        $this->environmentFile->setKey('DB_PORT', $dbPort);
        $this->environmentFile->setKey('DB_DATABASE', $dbSchema);
        $this->environmentFile->setKey('DB_USERNAME', $dbUser);
        $this->environmentFile->setKey('DB_PASSWORD', $dbPass);
    }

    public function setupSmtpEmail()
    {
        $usingMailtrap = $this->environmentFile->getValue('MAIL_HOST') === 'smtp.mailtrap.io';
        if ($this->confirm('Do you want to use mailtrap?', $usingMailtrap)) {
            $this->environmentFile->setKey('MAIL_HOST', 'smtp.mailtrap.io');
            $this->environmentFile->setKey('MAIL_PORT', '2525');
            $mailUser = $this->ask(
                'With which user do you want to connect to mailtrap?',
                $this->environmentFile->getValue('MAIL_USERNAME')
            );
            $this->environmentFile->setKey('MAIL_USERNAME', $mailUser);
            $mailPass = $this->ask(
                'With which pass do you want to connect to mailtrap?',
                $this->environmentFile->getValue('MAIL_PASSWORD')
            );
            $this->environmentFile->setKey('MAIL_PASSWORD', $mailPass);
            $mailDomain = $this->ask(
                'With which domain do you want to send emails?',
                $this->environmentFile->getValue('PORTAL_MAILS_DOMAIN')
            );
            $this->environmentFile->setKey('PORTAL_MAILS_DOMAIN', $mailDomain);
            $this->environmentFile->setKey(
                'MAIL_FROM_ADDRESS',
                $this->environmentFile->getValue('MAIL_FROM_ADDRESS')
            );
            $this->environmentFile->setKey(
                'MAIL_FROM_NAME',
                $this->environmentFile->getValue('MAIL_FROM_NAME')
            );
        } else {
            $mailHost = $this->ask(
                'To which host is the mail connected?',
                $this->environmentFile->getValue('MAIL_HOST')
            );
            $mailPort = $this->ask(
                'To which port is the mail connected?',
                $this->environmentFile->getValue('MAIL_PORT')
            );
            $mailUser = $this->ask(
                'With which user do you want to connect to mail?',
                $this->environmentFile->getValue('MAIL_USERNAME')
            );
            $mailPass = $this->ask(
                'With which pass do you want to connect to mail?',
                $this->environmentFile->getValue('MAIL_PASSWORD')
            );
            $fromAddress = $this->ask(
                'With which name do you want to send emails?',
                $this->environmentFile->getValue('MAIL_FROM_ADDRESS')
            );
            $fromName = $this->ask(
                'With which name do you want to send emails?',
                $this->environmentFile->getValue('MAIL_FROM_NAME')
            );
            $mailDomain = $this->environmentFile->setKey(
                'With which domain do you want to send emails?',
                $this->environmentFile->getValue('PORTAL_MAILS_DOMAIN')
            );
            $this->environmentFile->setKey('MAIL_HOST', $mailHost);
            $this->environmentFile->setKey('MAIL_PORT', $mailPort);
            $this->environmentFile->setKey('MAIL_USERNAME', $mailUser);
            $this->environmentFile->setKey('MAIL_PASSWORD', $mailPass);
            $this->environmentFile->setKey('MAIL_FROM_ADDRESS', $fromAddress);
            $this->environmentFile->setKey('MAIL_FROM_NAME', $fromName);
            $this->environmentFile->setKey('PORTAL_MAILS_DOMAIN', $mailDomain);
        }
    }


    public function setupFirstSystemUser(): array
    {
        $firstName = $this->ask('What name do you want for the first system user?', 'System');
        $lastName = $this->ask('What last name do you want for the first system user?', 'Admin');
        $email = $this->ask('What email do you want for the first system user?', 'support@rcdevelopment.de');
        $password = $this->ask('What password do you want for the first system user?', 'Aa123654');
        $systemUserId = \DB::table('users')->insertGetId([
            'code' => 'SYS-0000',
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => bcrypt($password),
            'status_id' => 1,
            'password_updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        \DB::table('settings')->where('key', 'domain')->update(['value' => $this->domain, 'active' => 1 ]);
        \DB::table('settings')->where('key', 'email')->update(['value' => $email, 'active' => 1 ]);
        \DB::table('settings')->where('key', 'color')->update(['value' => '#ec4640', 'active' => 1 ]);
        \DB::table('settings')->where('key', 'timezone')->update(['value' => 'Europe/Berlin', 'active' => 1 ]);

        return [
            'id' => $systemUserId,
            'code' => 'SYS-0000',
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => bcrypt($password),
            'status_id' => 1,
            'password_updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];
    }

    public function runFreshMigrations()
    {
        if ($this->isFirstInstall && $this->confirm('Do you want to erase all data an run all the migrations? ALL DATA WILL BE ERASED')) {
            $this->line('Running fresh migrations');
            $this->call('migrate:fresh');
        }
    }

    public function generateKey()
    {
        return 'base64:'.base64_encode(
            Encrypter::generateKey($this->laravel['config']['app.cipher'])
        );
    }

    public function getDefaultIndexEnvironment()
    {
        foreach ($this->environments as $key => $value) {
            if ($value === $this->environmentFile->getValue('APP_ENV')) {
                return $key;
            }
        }
        throw new \Exception("Environment {$this->environmentFile->getValue('APP_ENV')} not found on .env");
    }

    public function getDefaultIndexLogChannel()
    {
        foreach ($this->logChannels as $key => $value) {
            if ($value === $this->environmentFile->getValue('LOG_CHANNEL')) {
                return $key;
            }
        }
        throw new \Exception("Environment {$this->environmentFile->getValue('LOG_CHANNEL')} not found on .env");
    }
}
