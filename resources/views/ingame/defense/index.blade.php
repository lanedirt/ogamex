@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <!-- JAVASCRIPT -->
    <script type="text/javascript">
        function initResources() {
            var load_done = 1;
            gfSlider = new GFSlider(getElementByIdWithCache('planet'));
        }
        var action = 0;
        var id;
        var priceBuilding = 750;
        var priceShips = 750;
        var loca = loca || {};
        loca = $.extend({}, loca, {
            "error": "Error",
            "errorNotEnoughDM": "Not enough Dark Matter available! Do you want to buy some now?",
            "notice": "Reference"
        });
        var locaPremium = {
            "buildingHalfOverlay": "Do you want to reduce the construction time by 50% of the total construction time () for <b>750 Dark Matter<\/b>?",
            "buildingFullOverlay": "Do you want to immediately complete the construction order for <b>750 Dark Matter<\/b>?",
            "shipsHalfOverlay": "Do you want to reduce the construction time by 50% of the total construction time () for <b>750 Dark Matter<\/b>?",
            "shipsFullOverlay": "Do you want to immediately complete the construction order for <b>750 Dark Matter<\/b>?"
        };
        var demolish_id;
        var buildUrl;
        function loadDetails(type) {
            url = "{{ route('resources.index', ['ajax' => 1]) }}";
            if (typeof(detailUrl) != 'undefined') {
                url = detailUrl;
            }
            $.get(url, {type: type}, function (data) {
                $("#detail").html(data);
                $("#techDetailLoading").hide();
                $("input[type='text']:first", document.forms["form"]).focus();
                $(document).trigger("ajaxShowElement", (typeof techID === 'undefined' ? 0 : techID));
            });
        }
        function sendBuildRequest(url, ev, showSlotWarning) {
            console.debug("sendBuildRequest");
            if (ev != undefined) {
                var keyCode;
                if (window.event) {
                    keyCode = window.event.keyCode;
                } else if (ev) {
                    keyCode = ev.which;
                } else {
                    return true;
                }
                console.debug("KeyCode: " + keyCode);
                if (keyCode != 13 || $('#premiumConfirmButton')) {
                    return true;
                }
            }
            function build() {
                if (url == null) {
                    sendForm();
                } else {
                    fastBuild();
                }
            }

            if (url == null) {
                fallBackFunc = sendForm;
            } else {
                fallBackFunc = build;
                buildUrl = url;
            }
            if (showSlotWarning) {
                build();
            } else {
                build();
            }
            return false;
        }
        function fastBuild() {
            location.href = buildUrl;
            return false;
        }
        function sendForm() {
            document.form.submit();
            return false;
        }
        function demolishBuilding(id, question) {
            demolish_id = id;
            question += "<br/><br/>" + $("#demolish" + id).html();
            errorBoxDecision("Caution", "" + question + "", "yes", "No", demolishStart);
        }
        function demolishStart() {
            window.location.replace("{{ route('resources.index', ['modus' => 3]) }}&token=9c8a2a05984ebfd30e88ea2fd9da03df&type=" + demolish_id);
        }
        $(document).ready(function () {
            $('#ranks tr').hover(function () {
                $(this).addClass('hover');
            }, function () {
                $(this).removeClass('hover');
            });
        });
        var timeDelta = 1514117983000 - (new Date()).getTime();
        var LocalizationStrings = {
            "timeunits": {
                "short": {
                    "year": "y",
                    "month": "m",
                    "week": "w",
                    "day": "d",
                    "hour": "h",
                    "minute": "m",
                    "second": "s"
                }
            },
            "status": {"ready": "done"},
            "decimalPoint": ".",
            "thousandSeperator": ".",
            "unitMega": "Mn",
            "unitKilo": "K",
            "unitMilliard": "Bn",
            "question": "Question",
            "error": "Error",
            "loading": "load...",
            "yes": "yes",
            "no": "No",
            "ok": "Ok",
            "attention": "Caution",
            "outlawWarning": "You are about to attack a stronger player. If you do this, your attack defences will be shut down for 7 days and all players will be able to attack you without punishment. Are you sure you want to continue?",
            "lastSlotWarningMoon": "This building will use the last available building slot. Expand your Lunar Base to receive more space. Are you sure you want to build this building?",
            "lastSlotWarningPlanet": "This building will use the last available building slot. Expand your Terraformer or buy a Planet Field item to obtain more slots. Are you sure you want to build this building?",
            "forcedVacationWarning": "Some game features are unavailable until your account is validated.",
            "moreDetails": "More details",
            "lessDetails": "Less detail",
            "planetOrder": {
                "lock": "Lock arrangement",
                "unlock": "Unlock arrangement"
            },
            "darkMatter": "Dark Matter",
            "activateItem": {
                "upgradeItemQuestion": "Would you like to replace the existing item? The old bonus will be lost in the process.",
                "upgradeItemQuestionHeader": "Replace item?"
            }
        };
        var production_listid;
        $(document).ready(function () {
            initEventTable();
        });
        var player = {hasCommander: false};
        var detailUrl = "{{ route('defense.ajax') }}";

        $(document).ready(function () {
            initResources();
            @if (!empty($build_active['id']))
            // Countdown for inline building element (pusher)
            new shipCountdown(getElementByIdWithCache('shipAllCountdown'), getElementByIdWithCache('shipCountdown'), getElementByIdWithCache('shipSumCount'), {{ $build_active['time_countdown'] }}, {{ $build_active['time_countdown_object_single'] }}, {{ $build_queue_countdown }}, {{ $build_active['object_amount_remaining'] }}, "{{ route('defense.index') }}");
            @endif
        });

    </script>

    <div id="eventboxContent" style="display: none">
        <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif">
    </div>

    <div id="inhalt">
        <div id="planet" style="background-image:url(img/headers/defense/defense.jpg)">
            <div id="header_text">


                <h2>Defence - {{ $planet_name }}</h2>

            </div>

            <form method="POST" action="{!! route('defense.addbuildrequest') !!}" name="form" onkeyup="sendBuildRequest(null, event, false);" onsubmit="return false;">
                {{ csrf_field() }}
                <div id="detail" class="detail_screen">
                    <div id="techDetailLoading"></div>
                </div>
            </form>

        </div>
        <div class="c-left"></div>
        <div class="c-right"></div>
        <div id="buttonz">
            <div class="header">
                <h2>Defensive structures</h2>
            </div>
            <div class="content">
                <ul id="defensebuilding">
                    @foreach ($units[0] as $building)
                        <li id="defense{{ $building['count'] }}" class="@if ($building['currently_building'])
                                on
                            @elseif (!$building['requirements_met'])
                                off
                            @elseif (!$building['enough_resources'])
                                disabled
                            @else
                                on
                            @endif">
                            <div class="item_box defense{{ $building['id'] }}">
                                <div class="buildingimg">
                                    <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="{{ $building['title'] }} (0)@if (!$building['requirements_met'])
                                            <br/>Requirements are not met
                                            @endif" ref="{{ $building['id'] }}" id="details{{ $building['id'] }}" href="javascript:void(0);">
                        <span class="ecke">
                            <span class="level">
                                <span class="textlabel">
                                    {{ $building['title'] }}	                                </span>
                                {{ $building['amount'] }}	                            </span>
                        </span>
                                    </a>
                                </div>
                            </div>
                        </li>
                @endforeach

                    <!--<li id="defense1" class="off">
                        <div class="item_box defense401">
                            <div class="buildingimg">
                                <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="Rocket Launcher (0)<br/>Requirements are not met" ref="401" id="details401" href="javascript:void(0);">
                    <span class="ecke">
                        <span class="level">
                            <span class="textlabel">
                                Rocket Launcher                                </span>
                            0                            </span>
                    </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li id="defense2" class="off">
                        <div class="item_box defense402">
                            <div class="buildingimg">
                                <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="Light Laser (0)<br/>Requirements are not met" ref="402" id="details402" href="javascript:void(0);">
                    <span class="ecke">
                        <span class="level">
                            <span class="textlabel">
                                Light Laser                                </span>
                            0                            </span>
                    </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li id="defense3" class="off">
                        <div class="item_box defense403">
                            <div class="buildingimg">
                                <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="Heavy Laser (0)<br/>Requirements are not met" ref="403" id="details403" href="javascript:void(0);">
                    <span class="ecke">
                        <span class="level">
                            <span class="textlabel">
                                Heavy Laser                                </span>
                            0                            </span>
                    </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li id="defense4" class="off">
                        <div class="item_box defense404">
                            <div class="buildingimg">
                                <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="Gauss Cannon (0)<br/>Requirements are not met" ref="404" id="details404" href="javascript:void(0);">
                    <span class="ecke">
                        <span class="level">
                            <span class="textlabel">
                                Gauss Cannon                                </span>
                            0                            </span>
                    </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li id="defense5" class="off">
                        <div class="item_box defense405">
                            <div class="buildingimg">
                                <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="Ion Cannon (0)<br/>Requirements are not met" ref="405" id="details405" href="javascript:void(0);">
                    <span class="ecke">
                        <span class="level">
                            <span class="textlabel">
                                Ion Cannon                                </span>
                            0                            </span>
                    </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li id="defense6" class="off">
                        <div class="item_box defense406">
                            <div class="buildingimg">
                                <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="Plasma Turret (0)<br/>Requirements are not met" ref="406" id="details406" href="javascript:void(0);">
                    <span class="ecke">
                        <span class="level">
                            <span class="textlabel">
                                Plasma Turret                                </span>
                            0                            </span>
                    </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li id="defense7" class="off">
                        <div class="item_box defense407">
                            <div class="buildingimg">
                                <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="Small Shield Dome (0)<br/>Requirements are not met" ref="407" id="details407" href="javascript:void(0);">
                    <span class="ecke">
                        <span class="level">
                            <span class="textlabel">
                                Small Shield Dome                                </span>
                            0                            </span>
                    </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li id="defense8" class="off">
                        <div class="item_box defense408">
                            <div class="buildingimg">
                                <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="Large Shield Dome (0)<br/>Requirements are not met" ref="408" id="details408" href="javascript:void(0);">
                    <span class="ecke">
                        <span class="level">
                            <span class="textlabel">
                                Large Shield Dome                                </span>
                            0                            </span>
                    </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li id="defense9" class="off">
                        <div class="item_box defense502">
                            <div class="buildingimg">
                                <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="Anti-Ballistic Missiles (0)<br/>Requirements are not met" ref="502" id="details502" href="javascript:void(0);">
                    <span class="ecke">
                        <span class="level">
                            <span class="textlabel">
                                Anti-Ballistic Missiles                                </span>
                            0                            </span>
                    </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li id="defense10" class="off">
                        <div class="item_box defense503">
                            <div class="buildingimg">
                                <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="Interplanetary Missiles (0)<br/>Requirements are not met" ref="503" id="details503" href="javascript:void(0);">
                    <span class="ecke">
                        <span class="level">
                            <span class="textlabel">
                                Interplanetary Missiles                                </span>
                            0                            </span>
                    </span>
                                </a>
                            </div>
                        </div>
                    </li>-->

                </ul>
                <br class="clearfloat">
                <div class="footer"></div>
            </div>
        </div>

        <div id="line">
            {{-- Building is actively being built. --}}
            @if (!empty($build_active['id']))
                <div class="content-box-s">
                    <div class="header"><h3>Current production:</h3></div>
                    <div class="content">
                        <table cellspacing="0" cellpadding="0" class="construction active">
                            <tbody>
                            <tr class="data">
                                <th colspan="2">{{ $build_active['object']['title'] }}</th>
                            </tr>
                            <tr class="data">
                                <td title="Production of {{ $build_active['object_amount_remaining'] }} {{ $build_active['object']['title'] }} in progress" class="building tooltip" rowspan="2" valign="top">
                                    <a href="{{ route('shipyard', ['openTech' => 210]) }}" onclick="$('.detail_button[ref=210]').click(); return false;">
                                        <img class="queuePic" width="40" height="40" alt="{{ $build_active['object']['title'] }}" src="{{ asset('img/objects/units/' . $build_active['object']['assets']['img']['small']) }}"></a>
                                    <div class="shipSumCount" id="shipSumCount">{{ $build_active['object_amount_remaining'] }}</div>
                                </td>
                                <td class="desc timeProdShip">
                                    Building duration <span class="shipCountdown" id="shipCountdown">{{ $build_active['time_countdown'] }}</span>
                                </td>
                            </tr>
                            <tr class="data">
                                <td class="desc timeProdAll">
                                    Total time: <br><span class="shipAllCountdown" id="shipAllCountdown">{{ $build_queue_countdown }}</span>
                                </td>
                            </tr>
                            <tr class="data">
                                <td colspan="2">
                                    <a class="build-faster dark_highlight tooltipLeft js_hideTipOnMobile ships " title="Reduces construction time by 50% of the total construction time (9s)." href="javascript:void(0);" rel="{{ route('inventory', ['buyAndActivate' => '75accaa0d1bc22b78d83b89cd437bdccd6a58887']) }}">
                                        <div class="build-faster-img" alt="Halve time"></div>
                                        <span class="build-txt">Halve time</span>
                            <span class="dm_cost ">
                                Costs: 750 DM                            </span>
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <table class="queue">
                            <tbody><tr>

                            </tr>
                            </tbody></table>


                    </div>
                    <div class="footer"></div>
                </div>
            @endif
            @if (count($build_queue) > 0)
                <div id="pqueue">
                    <div class="header"><h3><span>Production queue</span></h3></div>
                    <div class="body">
                        <ul class="item">
                            @foreach ($build_queue as $item)
                                <li class="tooltip" title="{{ $item['object_amount'] }} {{ $item['object']['title'] }}<br>Building duration {{ $item['time_total'] }}s">


                                    <a class="slideIn" ref="210" href="javascript:void(0);">
                                        <img width="40" height="40" src="{{ asset('img/objects/units/' . $item['object']['assets']['img']['small']) }}">
                                    </a>
                                    <span class="number">{{ $item['object_amount'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="clearfloat"></div>
                    </div>        <div class="footer"></div>
                </div>
            @endif
            <div class="clearfloat"></div>
        </div>
    </div>

@endsection