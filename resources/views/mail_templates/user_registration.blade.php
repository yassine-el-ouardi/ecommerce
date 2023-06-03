<div style="max-width: 600px; ">
    {{__('lang.hello')}} <i>{{ $data->receiver }}</i>,
    <h1>{{__('lang.almost_there')}}</h1>
    <h4>{{__('lang.created_account')}}</h4>
    <p>{{__('lang.verify_email')}}</p>
    <p>{{__('lang.code_below')}}</p>

    <p>
        <span style="background: #39AEA4; padding: 10px 30px; margin: 20px 0; display: inline-block;
                        border-radius: 100px; font-size: 20px; color: #fff; letter-spacing: 1px;">
            <b>{{ $data->code }}</b>
        </span>
    </p>

    Thank You
    <h3>{{ $data->store_name  }}</h3>

    <div style="margin-top: 20px; border-top: 1px solid #eee;">
        <p>{{ $data->address }}</p>
        <p>{{__('lang.phone')}}: <a href="tel:{{ $data->phone}}">{{ $data->phone}}</a></p>
    </div>

</div>
