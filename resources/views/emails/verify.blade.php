@extends('templates.emails.model')

@section('greetings')
    Cher {{ $data['fullname'] }},
@endsection

@section('content')
    <p style="font-size: 14px; line-height: 200%;">
        <span style="font-size: 16px; line-height: 32px;">
            Nous sommes ravis que vous vous inscriviez. Tout d'abord, vous devez confirmer votre compte. 
            Le lien de validation expire après 60min.
            Cliquez sur le bouton ci-dessous pour valider votre compte et accéder à toutes les fonctionnalités 
            de notre plateforme :
        </span>
    </p>
    <p style="font-size: 14px; line-height: 200%;">
        <span style="font-size: 16px; line-height: 32px;">
            Veuillez cliquer sur le lien ci-dessous pour vérifier votre adresse e-mail :
        </span>
    </p>
@endsection