<?php

namespace App\Http\Controllers;

use App\Mail\MessageGoogle;
use App\Mail\SendMail;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendNotifyController extends Controller
{
    public $emails = [];

    // Envoi du mail aux utilisateurs
	//public function sendMessageGoogle ($data, $destinaire) {
    public function sendMessageGoogle (Request $request) {


        $data = $request->message;
        $titre = $request->titre;

		#3. Envoi du mail
		//$mail = Mail::to($email)->bcc("kouadiodeki@gmail.com")

		/* $mail = Mail::to($request->email)
						->queue(new MessageGoogle($titre, $data, env('MAIL_FROM_ADDRESS'))); */
        $mail = Mail::to($request->email)
						->queue(new SendMail($titre, $data, env('MAIL_FROM_ADDRESS')));

		return [
            "_status" => 1,
            '_message' => 'Mail envoyé à l\'adresse ',
            '_destinataires' => $request->email
        ];
	}
}
