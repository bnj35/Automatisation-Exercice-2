<?php

namespace App\Console;

use Faker\Factory;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Office;
use Illuminate\Support\Facades\Schema;
use Slim\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class PopulateDatabaseCommand extends Command
{
    private App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('db:populate');
        $this->setDescription('Populate database');
    }

    protected function execute(InputInterface $input, OutputInterface $output ): int
    {

        $output->writeln('Populate database...');

        /** @var \Illuminate\Database\Capsule\Manager $db */
        $db = $this->app->getContainer()->get('db');

        $db->getConnection()->statement("SET FOREIGN_KEY_CHECKS=0");
        $db->getConnection()->statement("TRUNCATE `employees`");
        $db->getConnection()->statement("TRUNCATE `offices`");
        $db->getConnection()->statement("TRUNCATE `companies`");
        $db->getConnection()->statement("SET FOREIGN_KEY_CHECKS=1");

        $faker = Factory::create("fr_FR");

        $companies = [];
        for ($i = 1; $i <= 4; $i++) {
            $companies[] = [
                'id' => $i,
                'name' => $faker->company,
                'phone' => $faker->phoneNumber,
                'email' => $faker->companyEmail,
                'website' => $faker->url,
                'image' => $faker->imageUrl(800, 600, 'business'),
                'head_office_id' => null,
            ];
        }

        foreach ($companies as $company) {
            $db->table('companies')->insert($company);
        }

        $offices = [];
        $officeId = 1;
        foreach ($companies as $company) {
            for ($j = 1; $j <= rand(2, 3); $j++) {
                $offices[] = [
                    'id' => $officeId,
                    'name' => $faker->company . ' Office',
                    'address' => $faker->streetAddress,
                    'city' => $faker->city,
                    'zip_code' => $faker->postcode,
                    'country' => $faker->country,
                    'email' => $faker->companyEmail,
                    'phone' => $faker->phoneNumber,
                    'company_id' => $company['id'],
                ];
                $officeId++;
            }
        }

        foreach ($offices as $office) {
            $db->table('offices')->insert($office);
        }

        $employees = [];
        for ($k = 1; $k <= 10; $k++) {
            $employees[] = [
                'id' => $k,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'office_id' => $faker->randomElement(array_column($offices, 'id')),
                'email' => $faker->email,
                'phone' => $faker->phoneNumber,
                'job_title' => $faker->jobTitle,
            ];
        }

        foreach ($employees as $employee) {
            $db->table('employees')->insert($employee);
        }

        foreach ($companies as &$company) {
            $company['head_office_id'] = $faker->randomElement(array_column($offices, 'id'));
            $db->table('companies')->where('id', $company['id'])->update(['head_office_id' => $company['head_office_id']]);
        }

        $output->writeln('Database created successfully!');
        return 0;
    }
}
