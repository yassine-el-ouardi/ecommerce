@extends('layouts.email_layout')

@section('content')
    <p class="mb-10">{{__('lang.inform_you')}} <b>#{{ $order->order }}</b> {{__('lang.delivered')}}</p>
    <p>{{__('lang.enjoying_purchase')}}</p>

    <h5 class="mtb-20"><a class="btn" href="{{ env('ORDER_DETAIL_REDIRECT', \App\Models\Helper\Utils::orderDetailRedirect()) . '/' . $order->id }}">WRITE A REVIEW</a></h5>

    <table class="mb-10">
        <tr>
            <th class="pb-10">{{__('lang.shipped_to')}}</th>
        </tr>

        <tr>
            <td>
                @if (!is_null($order->address))
                    <h5 style="margin-bottom: 5px">{{ $order->address->name }}</h5>
                @else
                    <h5 style="margin-bottom: 5px">{{ $order->user->name }}</h5>
                @endif
                <p>{{ $order->formatted_address }}</p>
                <p>{{__('lang.email')}}: {{ $order->user->email }}</p>

                    @if (!is_null($order->address))
                        <p>{{__('lang.phone')}}: {{ $order->user->phone }}</p>
                    @endif


            </td>
        </tr>
    </table><!--table-->


    <table class="mb-10">
        <tr>
            <th class="pb-10">{{__('lang.paid_via')}}</th>
        </tr>

        <tr>
            <td>{{ $order->order_method }}</td>
        </tr>
    </table><!--table-->

    <table style="background: #eee; border: 1px solid #ddd; border-bottom: none" class="mt-20 main-table border-tr">
        <tr>
            <th>{{__('lang.title')}}</th>
            <th>{{__('lang.delivery_fee')}}</th>
            <th>{{__('lang.quantity')}}</th>
            <th>{{__('lang.price')}}</th>
            <th>{{__('lang.total')}}</th>
        </tr>

        @foreach ($order->ordered_products as $op)
            <tr style="background: #fff">
                <td>
                    <b>{{ $op->product->title }}</b>
                    <span class="mt-5 f-9 block">{{ \App\Models\Helper\MailHelper::generatingAttribute($op) }}</span>
                </td>
                <td>
                    {{ $setting->currency_icon }}
                    {{ \App\Models\Helper\MailHelper::shippingPrice($op->shipping_place, $op->shipping_type) }}
                </td>
                <td>{{ $op->quantity }}</td>

                <td>{{ $setting->currency_icon }}{{ $op->selling }}</td>
                <td>{{ $setting->currency_icon }}{{ $op->selling * $op->quantity }}</td>
            </tr>

        @endforeach
    </table><!--table-->

    <table class="border-tr td-right-align footer-table"
           style="border: 1px solid #ddd; background: #eee; ">
        <tr>
            <td style="width: 630px" >{{__('lang.subtotal')}}</td>
            <td style="width: 70px;">{{ $setting->currency_icon }}{{ $order->calculated_price['subtotal'] }}</td>
        </tr>
        <tr>
            <td>{{__('lang.shipping_cost')}}</td>
            <td>{{ $setting->currency_icon }}{{ $order->calculated_price['shipping_price'] }}</td>
        </tr>

        @if ((int) $order->calculated_price['bundle_offer'] > 0)
            <tr>
                <td>{{__('lang.bundle_offer')}}</td>
                <td>{{ $setting->currency_icon }}{{ $order->calculated_price['bundle_offer'] }}</td>
            </tr>
        @endif

        @if ((int) $order->calculated_price['voucher_price'] > 0)
            <tr>
                <td>{{__('lang.voucher')}}</td>
                <td>{{ $setting->currency_icon }}{{ $order->calculated_price['voucher_price'] }}</td>
            </tr>
        @endif

        @if ((int) $order->calculated_price['tax'] > 0)
            <tr>
                <td>{{__('lang.tax')}}</td>
                <td>{{ $setting->currency_icon }}{{ $order->calculated_price['tax'] }}</td>
            </tr>
        @endif

        <tr>
            <td>{{__('lang.total')}}</td>
            <td>{{ $setting->currency_icon }}{{ $order->calculated_price['total_price'] }}</td>
        </tr>
    </table>
@stop
