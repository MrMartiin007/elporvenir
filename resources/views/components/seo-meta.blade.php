{{-- SEO Meta Tags Component --}}
@props([
    'title' => 'Beauty Center El Porvenir - Cosméticos y Cuidado Personal en Puerto Barrios',
    'description' => 'Descubre los mejores productos de belleza y cuidado personal en Puerto Barrios, Izabal. Amplio catálogo de cosméticos, maquillaje, cuidado de la piel y más.',
    'keywords' => 'cosméticos, belleza, cuidado personal, maquillaje, Puerto Barrios, Izabal, Guatemala, productos de belleza, skincare',
    'image' => asset('logo.jpg'),
    'url' => url()->current(),
    'type' => 'website'
])

{{-- Basic Meta Tags --}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<meta name="author" content="Beauty Center El Porvenir">
<meta name="robots" content="index, follow">
<meta name="language" content="Spanish">
<meta name="geo.region" content="GT-IZ">
<meta name="geo.placename" content="Puerto Barrios">
<meta name="geo.position" content="15.7308;-88.5992">
<meta name="ICBM" content="15.7308, -88.5992">

{{-- Canonical URL --}}
<link rel="canonical" href="{{ $url }}">

{{-- Title --}}
<title>{{ $title }}</title>

{{-- Open Graph / Facebook --}}
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:site_name" content="Beauty Center El Porvenir">
<meta property="og:locale" content="es_GT">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $url }}">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">

{{-- WhatsApp --}}
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">

{{-- Favicon --}}
<link rel="shortcut icon" href="{{ asset('logo.jpg') }}" type="image/jpeg">
<link rel="apple-touch-icon" href="{{ asset('logo.jpg') }}">
