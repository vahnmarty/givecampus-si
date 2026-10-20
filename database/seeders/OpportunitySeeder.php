<?php

namespace Database\Seeders;

use App\Models\Opportunity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
class OpportunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file_path = database_path('json/op.json');

        $data = File::json($file_path);

        $records = $data['records'];

        foreach ($records as $json) {
             $opModel = Opportunity::where('salesforce_id', $json['Id'])->first();

            if (! $opModel) {
                $opModel = new Opportunity();
                $opModel->salesforce_id = $json['Id'];
                $opModel->name = $json['Name'];
                $opModel->amount = $json['Amount'];
                $opModel->stage_name = $json['StageName'] ?? null;
                $opModel->close_date = $json['CloseDate'] ?? null;
                $opModel->save();
            }


        }
    }
}
