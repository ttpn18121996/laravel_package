<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@lang('Log into the system')</title>
    <style>
        * { margin: 0; padding: 0; text-decoration: none; font-family: 'Montserrat', sans-serif; }
        body { min-height: 100vh; background-image: linear-gradient(120deg, #3498db, #8e44ad); }
        .login-form {
            width: 360px;
            background: #f1f1f1;
            height: 580px;
            padding: 80px 40px;
            border-radius: 10px;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
        .login-form h1 { text-align: center; margin-bottom: 60px; }
        .txtb { border-bottom: 2px solid #adadad; position: relative; margin-bottom: 30px; }
        .txtb input {
            font-size: 15px;
            color: #333;
            border: none;
            width: 100%;
            outline: none;
            background: none;
            padding: 0 5px;
            height: 40px;
        }
        .txtb span::before {
            content: attr(data-placeholder);
            position: absolute;
            top: 50%;
            left: 5px;
            color: #adadad;
            transform: translateY(-50%);
            z-index: -1;
            transition: .5s;
        }
        .txtb span::after {
            content: '';
            position: absolute;
            width: 0%;
            height: 2px;
            background: linear-gradient(120deg, #3498db, #8e44ad);
            transition: .5s;
        }
        .focus + span::before { top: -5px; }
        .focus + span::after { width: 100%; }
        .logbtn {
            display: block;
            width: 100%;
            height: 50px;
            border: none;
            background: linear-gradient(120deg, #3498db, #8e44ad, #3498db);
            background-size: 200%;
            color: #fff;
            outline: none;
            transition: .5s;
            cursor: pointer;
        }
        .logbtn:hover { background-position: right; }
        .bottom-text {  margin-top: 60px; text-align: center; font-size: 14px; }
        .bottom-text-error {
            margin-top: 2rem;
            text-align: center;
            font-size: 13px;
            background: #f75676;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        .bottom-text-error p { color: #fff; margin: 5px auto; }
        #remember + label { margin: 5px; cursor: pointer; }
        #remember { display: none; }
        #remember + label::before {
            content: '\2714';
            border: 1px solid #000;
            border-radius: 3px;
            display: inline-block;
            width: 16px;
            height: 16px;
            padding-left: 1px;
            padding-bottom: 2px;
            margin-right: 5px;
            vertical-align: bottom;
            color: transparent;
            transition: .2s;
        }
        #remember + label:active:before {transform: scale(0);}
        #remember:checked + label:before {
            background: #3498db;
            border: 1px solid #3498db;
            color: #fff;
        }
        .forgot-password{text-align: center;margin-top: 1rem;}
        .forgot-password a{color: #3994d9;}
        .forgot-password a:hover{color: #7d55b6;}
    </style>
</head>
<body>
    <form action="" method="POST" class="login-form">
        @csrf
        <h1>@lang('Login')</h1>
        <div class="txtb">
            <input type="text" name="username" id="username" autocomplete="off">
            <span data-placeholder="{{ __('Username') }}"></span>
        </div>
        <div class="txtb">
            <input type="password" name="password" id="password" autocomplete="off">
            <span data-placeholder="{{ __('Password') }}"></span>
        </div>
        <button type="submit" class="logbtn">@lang('Login')</button>
        <div class="forgot-password">
            <a href="#">@lang('Forgot password')?</a>
        </div>
        @if ($errors->any())
            <div class="bottom-text-error">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        @if (session('error'))
            <div class="bottom-text-error">
                <p>{{ session('error') }}</p>
            </div>
        @endif
    </form>
    <script type="text/javascript">
        let txtb = document.querySelectorAll('.txtb input');
        txtb.forEach(element => {
            element.addEventListener('focus', () => {
                element.classList.add("focus");
            });
            element.addEventListener('blur', () => {
                if (element.value === '') {
                    element.classList.remove("focus");
                }
            });
        });
    </script>
</body>
</html>
