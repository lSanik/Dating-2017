@extends('admin.layout')

@section('styles')
    <!-- ink href="{{ url('/assets/css/bootstrap-reset.css') }}" rel="stylesheet" -->
    <link href="{{ url('/assets/css/fileinput.css') }}" rel="stylesheet">
    <link href="{{ url('assets/css/datepicker.css') }}" rel="stylesheet">

    <style>
        .file-input.file-input-new,#status .file-input{
            position: relative !important;
        }
        .file-input{
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            top: 0;
            overflow: visible;
            background-color: white;

        }
        .red{
            color:red!important;
        }
    </style>

@stop

@section('content')

    <section class="panel">
        <header class="panel-heading">
            Добавить новую анкету
        </header>
        <div class="panel-body">
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#osn" aria-controls="osn" role="tab" data-toggle="tab" id="open_main">Основная информация профиля</a></li>
                <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab" id="open_additional">Данные профиля</a></li>
                <li role="presentation"><a href="#status" aria-controls="status" role="tab" data-toggle="tab" id="open_partner">Партнерская информация</a></li>
            </ul>
                {!! Form::open(['url' => 'admin/girl/store', 'class' => 'form', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
            <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="osn">
                        <div class="col-md-12 text-center">
                            <h3> Основная информация профиля </h3>
                            <div class="avatar_block col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('avatar', 'Аватар') !!}<br/>
                                        <div class="img_avatar" style="max-height: 445px;display: inline-block;width: 100%;"><img width="373rem" style="max-width: 100%;width: auto;height: auto;    max-height: 445px;" src="/uploads/empty_girl.png" id="preview-avatar"></div>
                                        <input type="file" class="form-control file" name="avatar" accept="image/*" value="/uploads/empty_girl.png">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group col-md-6">
                                        {!! Form::label('webcam', 'Вебкамера') !!}
                                        <input type="checkbox" name="webcam">
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('hot', 'Hot Block') !!}
                                        <input type="checkbox" name="hot">
                                    </div>
                                    <div class="form-group  col-md-6">
                                        <label for="first_name">Имя<span class="red">*</span></label>
                                        {!! Form::text('first_name', '', ['class'=>'form-control', 'placeholder' => 'Name', 'required'=>'required']) !!}
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="second_name">Фамилия<span class="red">*</span></label>
                                        {!! Form::text('second_name', '', ['class'=>'form-control', 'placeholder' => 'Surname', 'required'=>'required']) !!}
                                    </div>

                                    <div class="form-group col-md-12">
                                        <title>Дата рождения</title>
                                    <!--{!! Form::label('birthday', 'Дата рождения') !!}
                                    {!! Form::text('birthday', '', ['class' => 'form-control default-date-picker'/*, 'disabled' => 'disabled'*/]) !!}-->
                                        <div class="col-md-4" style="padding-left: 0;">
                                            <label for="b_year">Год рождения<span class="red">*</span></label>
                                            <select class="form-control" name="b_year" required="required" style="    padding: 0;" >
                                                <option>---</option>
                                                @for($i=date("Y")-100; $i<date("Y"); $i++)
                                                        <option>{!! $i !!}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="b_month" >Месяц<span class="red">*</span></label>
                                            <select class="form-control" name="b_month" required="required" style="    padding: 0;">
                                                @for($i=1; $i<13; $i++)
                                                        <option>{!! $i !!}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-4" style="padding-right: 0;">
                                            <label for="b_day">День<span class="red">*</span></label>
                                            <select class="form-control" style="    padding: 0;" required="required" name="b_day">
                                                @for($i=1; $i<32; $i++)
                                                        <option>{!! $i !!}</option>
                                                @endfor
                                            </select>
                                        </div>

                                    </div>

                                    <div class="info col-md-4">
                                        <div class="form-group">
                                            <label for="email">Email<span class="red">*</span></label>
                                            {!! Form::email('email', '', ['class' => 'form-control', 'required'=>'required', 'placeholder' => 'email@email.com', 'required' => 'required']) !!}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="phone">Phone<span class="red">*</span></label>
                                        {!! Form::text('phone', '', ['class' => 'form-control', 'required'=>'required' ]) !!}
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="password">Password<span class="red">*</span></label>
                                        {!! Form::password('password', ['class' => 'form-control', 'required'=>'required']) !!}
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="coutry">Cтрана<span class="red">*</span></label>
                                        <select name="county" class="form-control" required="required">
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id }}"> {{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="state">Штат<span class="red">*</span></label>
                                        {!! Form::hidden('user_state_id', '' ) !!}
                                        <select name="state" class="form-control" required="required"></select>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="city">Город<span class="red">*</span></label>
                                        {!! Form::hidden('user_city_id', '' ) !!}
                                        <select name="city" class="form-control" required="required"></select>
                                    </div>
                                </div>
                                <div class="form-group text-center col-md-12">
                                    <button class="btn btn-next next" onClick="next_click();">Далее</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="profile">
                        <div class="col-md-12">
                            <h3 class="text-center"> Дополнительная информация профиля </h3>
                            <div class="form-group col-md-4">
                                {!! Form::label('height', 'Рост (см)') !!}
                                {!! Form::text('height', '', ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('weight', 'Вес (кг)') !!}
                                {!! Form::text('weight', '', ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('occupation', 'Род деятельности') !!}
                                {!! Form::text('occupation', '', ['class' => 'form-control']) !!}
                            </div>
                        <!--div class="form-group col-md-4">
                                {!! Form::label('gender', 'Пол') !!}
                        {!! Form::select('gender', $selects['gender'], '' ,  ['class' => 'form-control']) !!}
                                </div-->
                            <input type="hidden" name="gender" value="female">
                            <div class="form-group col-md-4">
                                {!! Form::label('eye', 'Цвет глаз') !!}
                                {!! Form::select('eye', $selects['eye'] , '',  ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('hair', 'Цвет волос') !!}
                                {!! Form::select('hair', $selects['hair'], '',  ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('education', 'Образование') !!}
                                {!! Form::select('education', $selects['education'],'',  ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('kids', 'Дети') !!}
                                {!! Form::select('kids', $selects['kids'],'',  ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('kids_live', 'Проживание детей')!!}
                                {!! Form::select('kids_live', $selects['kids_live'],'',  ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('want_kids', 'Желание завести детей') !!}
                                {!! Form::select('want_kids', $selects['want_kids'],'',  ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('family', 'Семейное положение') !!}
                                {!! Form::select('family', $selects['family'], '',  ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('religion', 'Вероисповедание') !!}
                                {!! Form::select('religion', $selects['religion'], '',  ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('finance_income', 'Доход') !!}
                                {!! Form::select('finance_income', $selects['finance_income'], '',  ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('know_lang', 'Знание языков') !!}
                                {!! Form::text('know_lang','', ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('english_level', 'Знание Английского') !!}
                                {!! Form::select('english_level', $selects['english_level'], '',  ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('smoke', 'Отношение к курению') !!}
                                {!! Form::select('smoke', $selects['smoke'], '',  ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('drink', 'Отношение к алкоголюы') !!}
                                {!! Form::select('drink', $selects['drink'], '',  ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-12">
                                {!! Form::label('about', 'О девушке ') !!}
                                <textarea class="form-control" name="about" style="height: 152px;"></textarea>
                            </div>

                            <div class="form-group col-md-12" style="padding: 0;">
                                <div class="form-group col-md-8">
                                    {!! Form::label('looking', 'О партнере ') !!}
                                    <textarea class="form-control" name="looking" style="height: 152px;"></textarea>
                                </div>
                                <div class="form-group col-md-4">
                                    <div class="col-md-6">
                                        {!! Form::label('l_age_start', 'Возраст от:') !!}
                                        {!! Form::number('l_age_start','', ['class' => 'form-control']) !!}
                                    </div>

                                    <div class="col-md-6">
                                        {!! Form::label('l_age_stop', 'До:') !!}
                                        {!! Form::number('l_age_stop','', ['class' => 'form-control']) !!}
                                    </div>

                                    <div class="col-md-6">
                                        {!! Form::label('l_height_start', 'Рост (см) от:') !!}
                                        {!! Form::number('l_height_start','', ['class' => 'form-control']) !!}
                                    </div>

                                    <div class="col-md-6">
                                        {!! Form::label('l_height_stop', 'До:') !!}
                                        {!! Form::number('l_height_stop','', ['class' => 'form-control']) !!}
                                    </div>

                                    <div class="col-md-6">
                                        {!! Form::label('l_weight_start', 'Вес (кг) от:') !!}
                                        {!! Form::number('l_weight_start','', ['class' => 'form-control']) !!}
                                    </div>

                                    <div class="col-md-6">
                                        {!! Form::label('l_weight_stop', 'До:') !!}
                                        {!! Form::number('l_weight_stop','', ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    {!! Form::label('l_horoscope_id', 'Знак зодиака партнера:') !!}
                                    <select name="l_horoscope_id" id="l_horoscope_id" class="form-control">
                                        <option value="---">---</option>
                                        @foreach($zodiac_list as $key=>$zodiac)
                                                <option value="{!! $key !!}">{!! $zodiac !!}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group text-center col-md-12">
                                <button class="btn btn-next next" onclick="next_click1();">Далее</button>
                            </div>

                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="status">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('passno','№ паспорта') !!}
                                    {!! Form::text('passno', '', ['class' => 'form-control']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('pass_date', 'Дата выдачи паспорта') !!}
                                    {!! Form::date('pass_date', '' , ['class' => 'form-control' /*default-date-picker', 'id' => 'datepicker'*/]) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('avatar', 'Фото/Скан паспорта') !!}
                                    <br/>
                                    <img width="373rem" src="">
                                    <input type="file" class="form-control file" name="pass_photo" value=""  accept="image/*"><!--disabled="disabled"-->
                                </div>

                                <div class="form-group col-md-12 text-center">
                                    {!! Form::submit('Submit', ['class' => 'btn btn-success']) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                {!! Form::close() !!}
        </div>
    </section>

@stop

@section('scripts')
    <script type="text/javascript" src="{{ url('/assets/js/jquery-ui_jquery-ui-1.10.1.custom.min.js') }}"></script>

    <!--bootstrap picker-->
    <script type="text/javascript" src="{{ url('/assets/js/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
    <script type="text/javascript" src="{{ url('/assets/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js') }}"></script>

    <script type="text/javascript" src="{{ url('/assets/js/bootstrap-fileinput-master/js/fileinput.js') }}"></script>
    <script type="text/javascript" src="{{ url('/assets/js/file-input-init.js') }}"></script>

    <script>
        function next_click() {

                $('#open_additional').trigger('click');
                return false;

        }
        function next_click1() {

                $('#open_partner').trigger('click');
                return false;

        }
        $(function() {


            $('.default-date-picker').datepicker();


            $('select[name="county"]').on('change', function(){

                $('select[name="city"]').empty();

                $.ajax({
                    type: 'POST',
                    url: '{{ url('/get/states/')  }}',
                    data: {id: $(this).val(), _token: $('input[name="_token"]').val()  },
                    success: function( response ){
                        $('select[name="state"]').empty();
                        for( var i = 0; i < response.length; i++ )
                        {
                            $('select[name="state"]').append("<option value='" + response[i].id + "'>" + response[i].name + "</option>");
                        }
                    },
                    error: function( response ){
                        console.log( response )
                    }
                });

            });

            $('select[name="state"]').on('change', function(){

                $.ajax({
                    type: 'POST',
                    url: '{{ url('/get/cities/') }}',
                    data: {id: $(this).val(), _token: $('input[name="_token').val() },
                    success: function( response ){
                        $('select[name="city"]').empty();
                        for ( var i = 0; i < response.length; i++)
                        {
                            $('select[name="city"]').append("<option value='" + response[i].id + "'>" + response[i].name + "</option>");
                        }

                    },
                    error: function( response ){
                        console.log( response );
                    }
                });

            });
        });
    </script>
@stop