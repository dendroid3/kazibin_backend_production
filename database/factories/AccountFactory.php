<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AccountFactory extends Factory
{
   
    public function definition()
    {
        $types = [
            'Writing',
            'Transcription',
            'Bidding',
            'Chat Moderation'
        ];

        $origins = [
            'Kenyan',
            'US',
            'UK',
            'Canada'
        ];

        $titles = [
            'WritersBay',
            'UvoCorp',
            'RemoteTask',
            '4Writers',
            'Eduson',
            'Blue Corp',
            'WritersLab'
        ];

        return [
            'user_id' => $this -> getUserId(),
            'code' => $this -> fakeCode(),
            'type' => $types[rand()&3],
            'title' => $titles[rand()&6],
            'profile_origin' => $origins[rand()&3],
            'profile_gender' => (rand()&1 == 1 ? 'Female' : 'Male'),
            'total_orders' => rand(200,2000),
            'pending_orders' => rand()&20,
            'amount_earned' => (rand(1, 1000) * 1000),
            'cost' => rand(15000,200000),
            'negotiable' => rand()&1,
            'display' => rand()&1,
            'rating' => rand(75,98),
            'expiry' => Carbon::now() -> addDays(7) -> toDateTimeString(),
            'description' => $this -> getDescription()
        ];
    }


    public function fakeCode(){
        return strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(3)) . rand(45,123);
    } 

    public function getUserId() {
        $users = User::all();
        $user_count = count($users);
        $user = $users[Floor(rand(0, ($user_count - 1)))];
        return $user -> id;
    }

    public function getDescription(){
        $text = "Lorem ipsum dolor sit, amet consectetur adipisicing elit. Praesentium, aperiam quis dolorum deserunt magnam animi iste laudantium dolorem, placeat doloremque tempore quasi nemo, saepe repellendus sit labore assumenda ipsam ratione recusandae eum numquam pariatur rem quos quisquam. Mollitia architecto suscipit assumenda sed hic laudantium dolorem sint, sunt molestias laboriosam perferendis! Vel, id aspernatur reprehenderit vero quia voluptatum dolores laborum distinctio et molestiae, eius animi incidunt perspiciatis iure. Laudantium explicabo quibusdam molestias natus consequatur fugit repellat modi nulla iste porro perspiciatis similique debitis, quae illum tempora. Officia repellendus magnam est quibusdam, illo sequi recusandae iure maxime similique molestiae velit nisi asperiores vero at libero nostrum corrupti veniam sit qui perspiciatis. Ea sapiente corporis ex ullam tenetur. Harum illum repellendus minima magni sapiente ratione laborum id quisquam soluta accusantium officia, dignissimos architecto veniam ea corrupti mollitia reprehenderit dolorem saepe incidunt? Doloribus culpa, cum voluptas aperiam perspiciatis tempore ea consequatur dignissimos? Temporibus ea in nostrum. Maxime fugit expedita voluptatum dignissimos nostrum porro veniam temporibus, est vel! Vel molestias nesciunt, earum ad sapiente unde consequatur exercitationem distinctio laudantium sint sed nulla iusto corrupti quisquam vitae. Non, excepturi aliquid veritatis, consequuntur nemo qui et tempora nisi repellendus impedit error, quos ipsum sunt blanditiis corporis dolore nobis quam quasi dolorem ad repellat assumenda animi! Nesciunt saepe tenetur autem recusandae voluptatem ipsa impedit aperiam hic deleniti quae maxime consequatur rem placeat numquam iure asperiores assumenda quos expedita praesentium, odio unde quia? Laboriosam, totam consequatur nihil labore ex corrupti dicta aut, quas veritatis esse necessitatibus excepturi et? Magni aliquam ea accusantium odio! Dolor nulla laudantium quas voluptatibus odio perferendis saepe fuga deleniti pariatur. Velit voluptatem mollitia repellat? Quae suscipit minima accusamus sequi harum corrupti error ipsum ducimus odio commodi, architecto quam fugiat recusandae et officiis veritatis reiciendis! Illo veniam voluptate facilis temporibus ad ex totam consequuntur provident a repudiandae quisquam ratione cupiditate laboriosam, ab perspiciatis cumque dolor ipsa qui quasi nihil eligendi praesentium nam? Quia excepturi vel perspiciatis, rerum facere exercitationem repellendus hic est vero illo quaerat minima deleniti quae odio dolorum, quas optio, commodi non alias ullam at sit repellat aliquam. Delectus minima voluptate tempore, illum debitis at, pariatur nemo veritatis, possimus molestias eum velit ex id tempora quae aspernatur labore ipsa. Ad eum soluta, neque nulla nesciunt ea tempore dolore, libero, facere cumque expedita nobis corporis voluptas vero. Cupiditate deserunt laborum quod temporibus, totam placeat vel porro earum nulla a recusandae quas quis voluptas! Omnis odio ad maxime fugit rem recusandae sapiente exercitationem velit in inventore temporibus consequuntur eveniet, veritatis consequatur similique, quos modi amet possimus officia incidunt! Non quaerat nostrum dolores harum facere quibusdam, ratione ullam amet! Magnam autem maxime molestias ab facilis illo dicta, facere numquam. Sit quisquam fuga laboriosam vel corrupti necessitatibus qui ducimus, dignissimos, aut non officiis neque. Ipsa non excepturi cum beatae. Beatae, atque. Qui, tempore suscipit sit quod deserunt quasi sequi totam saepe, rem quidem rerum esse mollitia amet possimus earum pariatur impedit! Porro soluta accusantium quis in, asperiores placeat exercitationem corrupti aperiam repellendus ullam dolorum, voluptas iste perspiciatis odit.";
        $minWords = 50;
        $maxWords = 200;
        $words = preg_split('/\s+/', $text);
        $totalWords = count($words);
        $wordCount = rand($minWords, min($maxWords, $totalWords));
        $start = rand(0, $totalWords - $wordCount);
        $randomWords = array_slice($words, $start, $wordCount);

        return implode(' ', $randomWords);
    }
}
