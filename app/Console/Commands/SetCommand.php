<?php

namespace App\Console\Commands;

use App\Helpers\TgBot;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Request;

class SetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $bot = (new TgBot)
            ->setApiUrl("https://api.telegram.org")
            ->setToken("5503460592:AAEwxWhg_hFMLYIW-mTA7DEYI-N-VDYiOyQ");
 //           ->setToken("889673981:AAEr8c6SXUan0-apuglTuFVt-M37QbBSvlM");
 //       $bot->setUrlWebhook("https://kolorovyi-svit.mk.ua/api/bot")
        $bot->setUrlWebhook("https://arhosa.win/bot/api/bot")
            ->setWebhook();
        $info = $bot->getWebhookInfo();
        //$info = [];
//try {
 //   $info = $bot->getChatMember('@kolorovyisvit', 5148942801);
 //       $info = $bot->getChatMember('@kolorovyisvit', 573372832);
    //$info = $bot->getChatMember("https://t.me/kolorovyisvit", 573372832);
 //       $info = $bot->getChatMember("@koloroviysvit1", 5148942801);
//} catch  (\Exception $e) {

//}
        print_r($info);
        return 0;
    }
}
