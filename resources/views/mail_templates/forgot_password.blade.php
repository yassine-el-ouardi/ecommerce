<div style="max-width: 600px; ">
    {{__('lang.hello')}} <i>{{ $data->receiver }}</i>,
    <h1>{{__('lang.almost_there')}}</h1>
    <p>{{__('lang.copy_code')}}</p>

    <p>
        <span style="background: #39AEA4; padding: 10px 30px; margin: 20px 0; display: inline-block;
                        border-radius: 100px; font-size: 20px; color: #fff; letter-spacing: 1px;">
            <b>{{ $data->code }}</b>
        </span>
    </p>

    {{__('lang.thank_you')}}
    <h3>{{ $data->store_name  }}</h3>

    <div style="margin-top: 20px; border-top: 1px solid #eee;">
        <p>{{ $data->address }}</p>
        <p>{{__('lang.phone')}}: <a href="tel:{{ $data->phone}}">{{ $data->phone}}</a></p>
    </div>

</div>
