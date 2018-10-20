@extends('layouts.master')
@section('header-title')
  <title>Contact | Vchip-edu</title>
@stop
@section('header-css')
    @include('layouts.home-css')
@stop
@section('header-js')
    @include('layouts.home-js')
@stop
@section('content')
    @include('header.header_menu')
<section class="container-fluid" style="margin-top: 120px;">
    <p>1.Is all the content that develop by Vchip-edu is free to use?</p>
    <p><b>Ans:</b> Yes, all the content develop by Vchip-edu’s core team is totally free. It is free service from Vchip-edu for society. But cost of any content developed by our client will be decided by them self, for their users.</p>
    <p>2.What is your earning as you don’t charge to your users?</p>
        <p><b>Ans:</b> We platform (both web-application and mobile app) to coaching institutes, on which they can start their digital education (ERP, LMS) within 30 minutes. Also, same platform for digital education and maintenance of their students records, we provide to colleges. These are paid services for colleges and coaching institutes. We also earn from advertisement on Vchip-edu platform. Also, Vchip Technology provide IT services, Web-site development, Mobile app developments, Digital marketing, SEO etc.</p>
    <p>3.What is motive of Vchip-edu?</p>
        <p><b>Ans:</b> The main motive of Vchip-edu is to provide quality education in remote area and villages along with urban area at free of cost. So, we conduct daily free live session on Basic of Mathematics, Logical reasoning and English.</p>
    <p>4.What about security of data of users and clients?</p>
        <p><b>Ans:</b> Vchip-edu platform is build on Amazon cloud service (AWS). Users data is encrypted by 256 bits. We know the value of your unique data, so we take full responsibly of our clients and users data.</p>
    <p>5.How can I contribute to Vchip-edu.</p>
        <p><b>Ans:</b> You can contribute to Vchip-edu, by taking some live session on basic of Math, Logic, and English, so we can fulfill the motive of the quality education to everyone.</p>
</section>
@stop
@section('footer')
    @include('footer.footer')
@stop