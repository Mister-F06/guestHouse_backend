@extends('templates.emails.model')

@section('greetings')
    Cher {{ $data['fullname'] }},
@endsection

@section('content')
    @if ($data['status'] == 'validated')
        <p style="font-size: 14px; line-height: 200%;">
            <span style="font-size: 16px; line-height: 32px;">
                Nous avons le plaisir de vous informer que la maison d'hôtes <strong> {{ $data['house_name'] }} </strong> que vous avez soumise a été approuvée par notre équipe. Félicitations ! Elle est désormais disponible sur notre plateforme.
            </span>
        </p>
        <p style="font-size: 14px; line-height: 200%;">
            <span style="font-size: 16px; line-height: 32px;">
                Vous pouvez consulter et gérer votre maison à tout moment en vous connectant à votre compte.
            </span>
        </p>
    @else
        <p style="font-size: 14px; line-height: 200%;">
            <span style="font-size: 16px; line-height: 32px;">
                Nous regrettons de vous informer que la maison d'hôtes <strong> {{ $data['house_name'] }} </strong> que vous avez soumise n'a pas été approuvée par notre équipe.
            </span>
        </p>
        <p style="font-size: 14px; line-height: 200%;">
            Malheureusement, votre soumission n'a pas répondu à nos critères de validation pour les raisons suivantes : <br>
            <span>
                {{ $data['reasons'] }}
            </span>
        </p>
        <p style="font-size: 14px; line-height: 200%;">
            <span style="font-size: 16px; line-height: 32px;">
                Si vous avez des questions ou souhaitez discuter des raisons du rejet, n'hésitez pas à nous contacter.
            </span>
        </p>
    @endif
@endsection
