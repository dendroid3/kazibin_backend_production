<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Service;
use Illuminate\Support\Facades\Log;

class SeedServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:services';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $services = [
            "Foreign Numbers" => [
                [
                    "name" => "Premium US Numbers",
                    "cost" => 1500
                ],
                
                [
                    "name" => "Non-Premium US numbers",
                    "cost" => 600
                ],
                
                [
                    "name" => "Premium Canada Numbers",
                    "cost" => 1500
                ]
            ],
        
            "AI Tools" => [
                [
                    "name" => "HIX Bypass bot",
                    "cost" => 1000
                ],
                
                [
                    "name" => "HIX Bypass Website",
                    "cost" => 1300
                ],
                
                [
                    "name" => "Stealth Writer Premium Bot",
                    "cost" => 1000
                ],
                
                [
                    "name" => "CHATGPT Plus Personal",
                    "cost" => 2000
                ],
                
                [
                    "name" => "CHATGPT plus Shared",
                    "cost" => 600
                ]
            ],
        
            "Similarity & AI Checkers" => [
                [
                    "name" => "TurnitinPro Account",
                    "cost" => 1200
                ],
                
                [
                    "name" => "Turnitin Instructor Account with AI Detector",
                    "cost" => 16000
                ],
                
                [
                    "name" => "Personal Turnitin Bot (30 Checks)",
                    "cost" => 500
                ],
                
                [
                    "name" => "Unlimited Personal Turnitin Bot",
                    "cost" => 1300
                ],
                
                [
                    "name" => "Student turnitin Personal (200+slots)",
                    "cost" => 300
                ],
                
                [
                    "name" => "Turnitin Instructor (Quick submit option)",
                    "cost" => 1200
                ]
            ],
        
            "Subscriptions" => [
                [
                    "name" => "Grammarly Premium/Business",
                    "cost" => 200
                ],
                
                [
                    "name" => "Quilbot Premium Monthly",
                    "cost" => 300
                ],
                
                [
                    "name" => "Edu Email",
                    "cost" => 1000
                ],
                
                [
                    "name" => "Canva Pro",
                    "cost" => 500
                ],
                
                [
                    "name" => "Coursera Premium",
                    "cost" => 5000
                ],
                
                [
                    "name" => "Microsoft 365 Premium",
                    "cost" => 1200
                ],
            ],
        
            "Unlock Services" => [
                [
                    "name" => "Chegg Unlock",
                    "cost" => 30
                ],
                
                [
                    "name" => "Coursehero Unlock",
                    "cost" => 50
                ],
                
                [
                    "name" => "Bartleby Unlock",
                    "cost" => 30
                ],
                
                [
                    "name" => "Studocu Unlock",
                    "cost" => 30
                ],
                
                [
                    "name" => "Scribd Unlock",
                    "cost" => 30
                ],
                
                [
                    "name" => "Numerade Unlock",
                    "cost" => 30
                ],
                
                [
                    "name" => "Brainly Unlock",
                    "cost" => 30
                ],
                
                [
                    "name" => "Slideshare Unlock",
                    "cost" => 30
                ],
                
                [
                    "name" => "Studypool Unlock",
                    "cost" => 70
                ],
                
                [
                    "name" => "Quizlet Unlock",
                    "cost" => 30
                ],
                
                [
                    "name" => "Cliffnotes Unlock",
                    "cost" => 70
                ]
            ],
        
            "Entertainment" => [
                [
                    "name" => "Netflix Premium",
                    "cost" => 500
                ],
                
                [
                    "name" => "DSTV Premium",
                    "cost" => 1500
                ]
            ],
        
            "VPNs & Proxies" => [
                [
                    "name" => "Windscribe VPN",
                    "cost" => 400
                ],
                
                [
                    "name" => "Personal Express VPN For 3 Months",
                    "cost" => 1500
                ],
                
                [
                    "name" => "Personal Nord VPN 3 Months",
                    "cost" => 1500
                ]
            ],
            
            "Reports & Other Services" => [
                [
                    "name" => "AI & Plag reports",
                    "cost" => 30
                ],
                
                [
                    "name" => "AI Removal",
                    "cost" => 150
                ]
            ]
        ];

        foreach ($services as $category => $service_list) {
            Log::info("Category: $category");
            foreach ($service_list as $service) {
                Log::info($service['cost']);
                $new_service = new Service;
                $new_service -> category = $category;
                $new_service -> name = $service['name'];
                $new_service -> cost = $service['cost'];
                $new_service -> save();
            }
        }
        
        return Service::count() . " Services Added";
    }
}
