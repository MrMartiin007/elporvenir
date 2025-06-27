<x-guest-layout>
    <style>
        body {
            color: #000;
            overflow-x: hidden;
            height: 100%;
            background-color: #B0BEC5;
            background-repeat: no-repeat;
        }

        .card0 {
            box-shadow: 0px 4px 8px 0px #757575;
            border-radius: 0px;
        }

        .card2 {
            margin: 0px 15px;
            /* Ajuste del margen para dispositivos móviles */
        }

        .logo {
            width: 200px;
            height: 100px;
            margin-top: 20px;
            margin-left: 35px;
        }

        .image {
            width: 100%;
            /* Hacer la imagen ancha al 100% del contenedor */
            max-width: 500px;
            /* Establecer un ancho máximo para evitar que sea demasiado grande */
            height: auto;
            /* Mantener la proporción original */
            margin-top: 20px;
        }

        .border-line {
            border-right: 1px solid #EEEEEE;
        }

        .text-sm {
            font-size: 14px !important;
        }

        ::placeholder {
            color: #BDBDBD;
            opacity: 1;
            font-weight: 300
        }

        :-ms-input-placeholder {
            color: #BDBDBD;
            font-weight: 300
        }

        ::-ms-input-placeholder {
            color: #BDBDBD;
            font-weight: 300
        }

        input,
        textarea {
            padding: 10px 12px 10px 12px;
            border: 1px solid lightgrey;
            border-radius: 2px;
            margin-bottom: 5px;
            margin-top: 2px;
            width: 100%;
            box-sizing: border-box;
            color: #2C3E50;
            font-size: 14px;
            letter-spacing: 1px;
        }

        input:focus,
        textarea:focus {
            -moz-box-shadow: none !important;
            -webkit-box-shadow: none !important;
            box-shadow: none !important;
            border: 1px solid #304FFE;
            outline-width: 0;
        }

        button:focus {
            -moz-box-shadow: none !important;
            -webkit-box-shadow: none !important;
            box-shadow: none !important;
            outline-width: 0;
        }

        a {
            color: inherit;
            cursor: pointer;
        }

        .btn-blue {
            background-color: #6f42c1;
            width: 100%;
            /* Hacer el botón ancho al 100% del contenedor */
            color: #fff;
            border-radius: 2px;
        }

        .btn-blue:hover {
            background-color: #000;
            cursor: pointer;
        }

        .bg-blue {
            color: #fff;
            background-color: pink;
        }

        @media screen and (max-width: 767px) {
            .logo {
                margin-left: 0;
                /* Ajuste del margen para dispositivos móviles */
            }

            .image {
                width: 100%;
                /* Hacer la imagen ancha al 100% del contenedor */
                max-width: 300px;
                /* Establecer un ancho máximo para evitar que sea demasiado grande */
                height: auto;
                /* Mantener la proporción original */
                margin-top: 0;
                /* Eliminar el margen superior */
            }

            .border-line {
                border-right: none;
            }

            .card2 {
                border-top: 1px solid #EEEEEE !important;
                margin: 0px 0px;
                /* Ajuste del margen para dispositivos móviles */
            }
        }
    </style>

    <div class="container-fluid px-1 px-md-5 px-lg-1 px-xl-5 py-5 mx-auto">
        <div class="card card0 border-0">
            <div class="row d-flex">
                <div class="col-lg-6">
                    <div class="card1 pb-5">
                        <div class="row px-3 justify-content-center mt-4 mb-5 border-line">
                            <img src="{{ asset('logo.jpg') }}"
                                class="image">
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 my-5">
                    <div class="card2 card border-0 px-4 py-5">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div>
                                <x-input-label for="email" :value="__('Ingrese Correo Electronico:')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                    :value="old('email')" required autofocus autocomplete="username" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="password" :value="__('Contraseña:')" />

                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                                    required autocomplete="current-password" />

                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div class="row px-3 mb-4">
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" name="chk" class="custom-control-input" name="remember"
                                        id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label for="remember" class="custom-control-label text-sm">Recordarme</label>
                                </div>

                                @if (Route::has('password.request'))
                                    <a class="ml-auto mb-0 text-sm" href="{{ route('password.request') }}">
                                        {{ __('Olvidé mi contraseña') }}
                                    </a>
                                @endif


                            </div>
                            <div class="row mb-4 px-3">
                                <button type="submit" class="btn btn-blue text-center">Iniciar Sesion</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="bg-blue py-4">
                <div class="row px-3">
                    <small class="ml-4 ml-sm-5 mb-2">Copyright &copy; 2019. All rights reserved.</small>
                    <div class="social-contact ml-4 ml-sm-auto">
                        <span class="fa fa-facebook mr-4 text-sm"></span>
                        <span class="fa fa-google-plus mr-4 text-sm"></span>
                        <span class="fa fa-linkedin mr-4 text-sm"></span>
                        <span class="fa fa-twitter mr-4 mr-sm-5 text-sm"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-guest-layout>