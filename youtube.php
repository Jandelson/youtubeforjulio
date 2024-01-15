<?php

use Google\Service\YouTube;
use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$client = new Google\Client();
$client->setApplicationName('Youtube-Data');
$client->setScopes([
    'https://www.googleapis.com/auth/youtube.readonly',
]);

$client->setDeveloperKey($_ENV['DEVKEY']);

$service = new YouTube($client);

$channels = [];
if(isset($_POST['channels'])) {
    $channels = explode(',', $_POST['channels']);
}

if(empty($channels)) {
    echo 'Channel not found...';
    echo "<form action='yotube.php' method='post'>
    <input type='text' size='40' name='channels' placeholder='channels split by comma'>
    <input type='submit' value='Get channels'>
    </form>";
}

foreach ($channels as $channel) {
    $queryParams = ['forUsername' => $channel];

    $response = $service->channels->listChannels('snippet,statistics', $queryParams);

    if (empty($response['items'])) {
        echo '<b>Informações não encontradas para o canal: ' . $channel . '</b>' . '<hr><br>';
    } else {
        foreach ($response['items'] as $item) {
            $img = $item['snippet']['thumbnails']['default']['url'];
            $title = $item['snippet']['localized']['title'];
            $statistics = $item['statistics'];
            print 'Canal: ' . $title . '<br>';
            print "<img src='$img' width='88' height='88'>" . '<br>';
            print '<b>Inscritos: ' . $statistics['subscriberCount'] . '</b><br>';
            print 'Vídeos: ' . $statistics['videoCount'] . '<br>';
            print 'Views: ' . $statistics['viewCount'] . '<br>';
            print '<hr><br>';
        }
    }
}
