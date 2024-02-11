@extends('layouts.app')

@section('content')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.8.1/css/bootstrap-select.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.8.1/js/bootstrap-select.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <!-- <link rel="stylesheet" type="text/css" href="css/bootstrap-datetimepicker.css"> -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Add server') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{url('/server-add')}}" method="POST">
                        @csrf
{{--                        <select name="monitoringName" id="" class="form-control">--}}
{{--                            <option value="" selected disabled>Мониторинг</option>--}}
{{--                            <option value="Topms">ms-top</option>--}}
{{--                        </select>--}}
                        <input type="text" class="form-control" name="title" placeholder="Имя сервера">
{{--                        <a href="https://freekassa.ru" target="_blank" rel="noopener noreferrer">--}}
{{--                            <img src="https://cdn.freekassa.ru/banners/big-dark-1.png" title="Прием платежей на сайте">--}}
{{--                        </a>--}}
                        <button type="submit" class="btn btn-sm btn-success">Добавить</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8 mt-4">
            <div class="card">
                <div class="card-header">{{ __('Купить буст') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{url('/buy')}}" method="POST">
                            @csrf
                            <label for="server">Выбрать сервер</label>
                            <select name="serverName" id="server" class="form-control">
                                <option value="◄ AKIMOFF YouTube ► 45.136.204.158:27015">◄ AKIMOFF YouTube ► 45.136.204.158:27015</option>
                                <option value="Test">Server 2</option>
                            </select>
{{--                            <select class="selectpicker form-control mt-4" name="monitoringName" multiple data-live-search="true" title="Выбрать мониторинг">--}}
{{--                                <option value="" selected disabled>Мониторинг</option>--}}
{{--                                <option data-hidden="true"></option>--}}
{{--                                <option value="Topms">ms-top</option>--}}
{{--                                <option value="Test">test</option>--}}
{{--                            </select>--}}
                            <div class="row mt-4" id="count-div">
                                <div class="col-md-4">
                                    <label for="" class="col-sm-12">Top-ms</label>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" name="monitorings[topms][round]" class="form-control col-sm-5">
                                </div>
                                <div class="col-md-4">
                                    <input type="checkbox" id="scales" name="monitorings[topms][selected]" style="width: 50px;height: 30px"/>
{{--                                    <input type="checkbox" name="monitorings[selected]" class="form-control">--}}
                                </div>
                            </div>
                            <div class="row mt-4" id="count-div">
                                <div class="col-md-4">
                                    <label for="" class="col-sm-12">Fine-boost</label>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" name="monitorings[fine-boost][round]" class="form-control col-sm-5">
                                </div>
                                <div class="col-md-4">
                                    <input type="checkbox" id="scales" name="monitorings[fine-boost][selected]" style="width: 50px;height: 30px"/>
                                    {{--                                    <input type="checkbox" name="monitorings[selected]" class="form-control">--}}
                                </div>
                            </div>
                            <div class="row mt-4" id="count-div">
                                <div class="col-md-4">
                                    <label for="" class="col-sm-12">cs-16</label>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" name="monitorings[cs16][round]" class="form-control col-sm-5">
                                </div>
                                <div class="col-md-4">
                                    <input type="checkbox" id="scales" name="monitorings[cs16][selected]" style="width: 50px;height: 30px"/>
                                    {{--                                    <input type="checkbox" name="monitorings[selected]" class="form-control">--}}
                                </div>
                            </div>
                            <div class="row mt-4" id="count-div">
                                <div class="col-md-4">
                                    <label for="" class="col-sm-12">cs-booster</label>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" name="monitorings[cs-booster][round]" class="form-control col-sm-5">
                                </div>
                                <div class="col-md-4">
                                    <input type="checkbox" id="scales" name="monitorings[cs-booster][selected]" style="width: 50px;height: 30px"/>
                                    {{--                                    <input type="checkbox" name="monitorings[selected]" class="form-control">--}}
                                </div>
                            </div>
                            <div class="row mt-4" id="count-div">
                                <div class="col-md-4">
                                    <label for="" class="col-sm-12">cs-clan</label>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" name="monitorings[cs-clan][round]" class="form-control col-sm-5">
                                </div>
                                <div class="col-md-4">
                                    <input type="checkbox" id="scales" name="monitorings[cs-clan][selected]" style="width: 50px;height: 30px"/>
                                    {{--                                    <input type="checkbox" name="monitorings[selected]" class="form-control">--}}
                                </div>
                            </div>
                            <div class='mt-2'>
                                <div class="form-group">
                                    <div class='input-group date' id='datetimepicker1'>
                                        <label for="datetimes">Дата</label>
                                        <input type="text" name="datetimes" class="form-control" id="datetimes"/>
{{--                                        <span class="input-group-addon">--}}
{{--                                            <span class="glyphicon glyphicon-calendar"></span>--}}
{{--                                        </span>--}}
                                    </div>
                                </div>
                            </div>
                            <div class='mt-2'>
                                <div class="form-group">
                                    <div class='input-group date' id='datetimepicker1'>
                                        <label for="time">Время</label>
                                        <input type="text" name="time" id="time" class="form-control" placeholder="12:00" value="12:00"/>
{{--                                        <span class="input-group-addon">--}}
{{--                                            <span class="glyphicon glyphicon-calendar"></span>--}}
{{--                                        </span>--}}
                                    </div>
                                </div>
                            </div>
{{--                            <input type="number" class="form-control" name="iteration" placeholder="Круги">--}}
                            <button type="submit" class="btn btn-sm btn-success">Купить</button>
                        </form>
                </div>
            </div>
        </div>
        <div class="col-md-8 mt-4">
            <div class="card">
                <div class="card-header">{{ __('Buy history') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                        <table class="table">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Service</th>
                                <th scope="col">Server</th>
                                <th scope="col">Rounds</th>
                                <th scope="col">Process datetime</th>
                                <th scope="col">Processed</th>
                                <th scope="col">Last message</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($buyHistory as $row)
                            <tr>
                                <th scope="row">1</th>
                                <td>{{$row->monitoring}}</td>
                                <td>{{$row->server_name}}</td>
                                <td>{{$row->iteration}}</td>
                                <td>{{$row->process_at}}</td>
                                @if($row->processed == 1)
                                    <td><button class="btn btn-success" style="pointer-events: none">Processed</button></td>
                                @else
                                    <td><button class="btn btn-warning" style="pointer-events: none">Pending</button></td>
                                @endif
                                <td>{{$row->response_msg}}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>
</div>
    <script>
        $(function () {
            // $('#datetimepicker1').datetimepicker();
            $(function() {
                $('input[name="datetimes"]').daterangepicker({
                    format: 'yyyy-mm-dd'
                });
                // $('input[name="time"]').timepicker();
            });
            $('select').selectpicker();
            $('.selectpicker').on('hidden.bs.select', function () {
                let values = $(this).val();
                values.forEach(function(item) {
                    $('#count-div').append(`<div class="col-md-6">
                                    <label for="" class="col-sm-5">${item}</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="number" name="monitorings[${item}]" class="form-control col-sm-5">
                                </div>`)
                });
            })
        });
    </script>
@endsection
