@extends('templates.emails.model')

@section('greetings')
    Cher {{ $data['fullname'] }},
@endsection

@section('content')
    <p style="font-size: 14px; line-height: 200%;">
        <span style="font-size: 16px; line-height: 32px;">
            Vous avez récemment demandé la réinitialisation de votre mot de passe. Pour procéder à cette étape, veuillez cliquer sur le lien ci-dessous :
        </span>
    </p>
    <p style="font-size: 14px; line-height: 200%;">
        <span style="font-size: 16px; line-height: 32px;">
            Si vous n'avez pas demandé cette réinitialisation ou si vous avez des questions, veuillez ignorer cet email.
        </span>
    </p>
@endsection