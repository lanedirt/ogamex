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
        var cancelProduction_id;
        var production_listid;
        function cancelProduction(id, listid, question) {
            cancelProduction_id = id;
            production_listid = listid;
            errorBoxDecision("Caution", "" + question + "", "yes", "No", cancelProductionStart);
        }
        function cancelProductionStart() {
            $('<form id="cancelProductionStart" action="{{ route('research.cancelbuildrequest') }}" method="POST" style="display: none;">{{ csrf_field() }}<input type="hidden" name="building_id" value="' + cancelProduction_id + '" /> <input type="hidden" name="building_queue_id" value="' + production_listid + '" /></form>').appendTo('body').submit();

            //window.location.replace("{!! route('research.cancelbuildrequest') !!}?_token=" + csrfToken + "&techid=" + cancelProduction_id + "&listid=" + production_listid);
        }
        $(document).ready(function () {
            initEventTable();
        });
        var player = {hasCommander: false};
        var detailUrl = "{{ route('research.ajax') }}";

        $(document).ready(function () {
            initResources();
            @if (!empty($build_active['id']))
            // Countdown for inline building element (pusher)
            var elem = getElementByIdWithCache("b_research{{ $build_active['object']['id'] }}");
            if(elem) {
                new bauCountdown(elem, {{ $build_active['time_countdown'] }}, {{ $build_active['time_total'] }}, "{{ route('research.index') }}");
            }
            @endif
        });

    </script>

    <div id="eventboxContent" style="display: none">
    <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif">
    </div>

    <div id="inhalt">

        <div id="planet" style="background-image:url(img/headers/research/research.jpg)">
            <div id="header_text">
                <h2>Research - {{ $planet_name }}</h2>
            </div>

            <form method="POST" action="{!! route('research.addbuildrequest') !!}" name="form">
                {{ csrf_field() }}
                <div id="detail" class="detail_screen">
                    <div id="techDetailLoading"></div>
                </div>
            </form>

        </div>
        <div class="c-left"></div>
        <div class="c-right"></div>

        <div id="buttonz" class="wrapButtons">
            <div id="wrapBattle" class="resLeft fleft">
                <h2>Basic research</h2>
                <ul id="base1" class="activate">
                    @foreach ($research[0] as $building)
                        <li class="@if ($building['currently_building'])
                                on
                            @elseif (!$building['requirements_met'])
                                off
                            @elseif (!$building['enough_resources'])
                                disabled
                            @elseif ($build_queue_max)
                                disabled
                            @else
                                on
                            @endif
                                ">
                            <div class="item_box research{!! $building['id'] !!}">
                                <div class="buildingimg">
                                    @if ($building['requirements_met'] && $building['enough_resources'])
                                        <a class="fastBuild tooltip js_hideTipOnMobile" title="Research {!! $building['title'] !!} level {!! ($building['current_level'] + 1) !!}" href="javascript:void(0);" onclick="sendBuildRequest('{!! route('research.addbuildrequest') !!}', null, 1);">
                                            <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="22" height="14">
                                        </a>
                                    @endif
                                    @if ($building['currently_building'])
                                        <div class="construction">
                                            <div class="pusher" id="b_research{{ $building['id'] }}" style="height:100px;">
                                            </div>
                                            <a class="slideIn timeLink" href="javascript:void(0);" ref="{{ $building['id'] }}">
                                                <span class="time" id="test" name="zeit"></span>
                                            </a>

                                            <a class="detail_button slideIn"
                                               id="details{{ $building['id'] }}"
                                               ref="{{ $building['id'] }}"
                                               href="javascript:void(0);">
            <span class="eckeoben">
                <span style="font-size:11px;" class="undermark"> {{ $building['current_level'] + 1 }}</span>
            </span>
            <span class="ecke">
                <span class="level">{{ $building['current_level'] }}</span>
            </span>
                                            </a>
                                        </div>
                                    @endif
                                    <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="{!! $building['title'] !!}" ref="{!! $building['id'] !!}" id="details" href="javascript:void(0);">
                        <span class="ecke">
                            <span class="level">
                               <span class="textlabel">
                                   {!! $building['title'] !!}
                               </span>
                                {!! $building['current_level'] !!}	                           </span>
                        </span>
                                    </a>
                                </div>
                            </div>
                        </li>
                    @endforeach

                    <!--
                    <li class="off">
                        <div class="item_box research113">
                            <div class="buildingimg">
                                <a href="javascript:void(0);" ref="113" id="details113" title="Energy Technology<br/>No research laboratory" class="detail_button tooltip js_hideTipOnMobile slideIn">
                                <span class="ecke">
                                    <span class="level">
                                        <span class="textlabel">Energy Technology </span>
                                        0                                        <span class="undermark">
                                                                                    </span>
                                    </span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="off">
                        <div class="item_box research120">
                            <div class="buildingimg">
                                <a href="javascript:void(0);" ref="120" id="details120" title="Laser Technology<br/>No research laboratory" class="detail_button tooltip js_hideTipOnMobile slideIn">
                                <span class="ecke">
                                    <span class="level">
                                        <span class="textlabel">Laser Technology </span>
                                        0                                        <span class="undermark">
                                                                                    </span>
                                    </span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="off">
                        <div class="item_box research121">
                            <div class="buildingimg">
                                <a href="javascript:void(0);" ref="121" id="details121" title="Ion Technology<br/>No research laboratory" class="detail_button tooltip js_hideTipOnMobile slideIn">
                                <span class="ecke">
                                    <span class="level">
                                        <span class="textlabel">Ion Technology </span>
                                        0                                        <span class="undermark">
                                                                                    </span>
                                    </span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="off">
                        <div class="item_box research114">
                            <div class="buildingimg">
                                <a href="javascript:void(0);" ref="114" id="details114" title="Hyperspace Technology<br/>No research laboratory" class="detail_button tooltip js_hideTipOnMobile slideIn">
                                <span class="ecke">
                                    <span class="level">
                                        <span class="textlabel">Hyperspace Technology </span>
                                        0                                        <span class="undermark">
                                                                                    </span>
                                    </span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="off">
                        <div class="item_box research122">
                            <div class="buildingimg">
                                <a href="javascript:void(0);" ref="122" id="details122" title="Plasma Technology<br/>No research laboratory" class="detail_button tooltip js_hideTipOnMobile slideIn">
                                <span class="ecke">
                                    <span class="level">
                                        <span class="textlabel">Plasma Technology </span>
                                        0                                        <span class="undermark">
                                                                                    </span>
                                    </span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    -->
                </ul>
            </div>
            <div id="wrapBattle" class="resRight fleft">
                <h2>Drive research</h2>
                <ul id="base2" class="activate">
                    @foreach ($research[1] as $building)
                        <li class="@if ($building['currently_building'])
                                on
                            @elseif (!$building['requirements_met'])
                                off
                            @elseif (!$building['enough_resources'])
                                disabled
                            @elseif ($build_queue_max)
                                disabled
                            @else
                                on
                            @endif
                                ">
                            <div class="item_box research{!! $building['id'] !!}">
                                <div class="buildingimg">
                                    @if ($building['requirements_met'] && $building['enough_resources'])
                                        <a class="fastBuild tooltip js_hideTipOnMobile" title="Research {!! $building['title'] !!} level {!! ($building['current_level'] + 1) !!}" href="javascript:void(0);" onclick="sendBuildRequest('{!! route('research.addbuildrequest') !!}', null, 1);">
                                            <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="22" height="14">
                                        </a>
                                    @endif
                                    @if ($building['currently_building'])
                                        <div class="construction">
                                            <div class="pusher" id="b_research{{ $building['id'] }}" style="height:100px;">
                                            </div>
                                            <a class="slideIn timeLink" href="javascript:void(0);" ref="{{ $building['id'] }}">
                                                <span class="time" id="test" name="zeit"></span>
                                            </a>

                                            <a class="detail_button slideIn"
                                               id="details{{ $building['id'] }}"
                                               ref="{{ $building['id'] }}"
                                               href="javascript:void(0);">
            <span class="eckeoben">
                <span style="font-size:11px;" class="undermark"> {{ $building['current_level'] + 1 }}</span>
            </span>
            <span class="ecke">
                <span class="level">{{ $building['current_level'] }}</span>
            </span>
                                            </a>
                                        </div>
                                    @endif
                                    <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="{!! $building['title'] !!}" ref="{!! $building['id'] !!}" id="details" href="javascript:void(0);">
                        <span class="ecke">
                            <span class="level">
                               <span class="textlabel">
                                   {!! $building['title'] !!}
                               </span>
                                {!! $building['current_level'] !!}	                           </span>
                        </span>
                                    </a>
                                </div>
                            </div>
                        </li>
                @endforeach
                    <!--<li class="off">
                        <div class="item_box research115">
                            <div class="buildingimg">
                                <a href="javascript:void(0);" ref="115" id="details115" title="Combustion Drive<br/>No research laboratory" class="detail_button tooltip js_hideTipOnMobile slideIn">
                                <span class="ecke">
                                    <span class="level">
                                        <span class="textlabel">Combustion Drive </span>
                                        0                                        <span class="undermark">
                                                                                    </span>
                                    </span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="off">
                        <div class="item_box research117">
                            <div class="buildingimg">
                                <a href="javascript:void(0);" ref="117" id="details117" title="Impulse Drive<br/>No research laboratory" class="detail_button tooltip js_hideTipOnMobile slideIn">
                                <span class="ecke">
                                    <span class="level">
                                        <span class="textlabel">Impulse Drive </span>
                                        0                                        <span class="undermark">
                                                                                    </span>
                                    </span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="off">
                        <div class="item_box research118">
                            <div class="buildingimg">
                                <a href="javascript:void(0);" ref="118" id="details118" title="Hyperspace Drive<br/>No research laboratory" class="detail_button tooltip js_hideTipOnMobile slideIn">
                                <span class="ecke">
                                    <span class="level">
                                        <span class="textlabel">Hyperspace Drive </span>
                                        0                                        <span class="undermark">
                                                                                    </span>
                                    </span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </li>-->
                </ul>
            </div>        <div id="wrapBattle" class="resLeft fleft">
                <h2>Advanced researches</h2>
                <ul id="base3" class="activate">
                    @foreach ($research[2] as $building)
                        <li class="@if ($building['currently_building'])
                                on
                            @elseif (!$building['requirements_met'])
                                off
                            @elseif (!$building['enough_resources'])
                                disabled
                            @elseif ($build_queue_max)
                                disabled
                            @else
                                on
                            @endif
                                ">
                            <div class="item_box research{!! $building['id'] !!}">
                                <div class="buildingimg">
                                    @if ($building['requirements_met'] && $building['enough_resources'])
                                        <a class="fastBuild tooltip js_hideTipOnMobile" title="Research {!! $building['title'] !!} level {!! ($building['current_level'] + 1) !!}" href="javascript:void(0);" onclick="sendBuildRequest('{!! route('research.addbuildrequest') !!}', null, 1);">
                                            <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="22" height="14">
                                        </a>
                                    @endif
                                    @if ($building['currently_building'])
                                        <div class="construction">
                                            <div class="pusher" id="b_research{{ $building['id'] }}" style="height:100px;">
                                            </div>
                                            <a class="slideIn timeLink" href="javascript:void(0);" ref="{{ $building['id'] }}">
                                                <span class="time" id="test" name="zeit"></span>
                                            </a>

                                            <a class="detail_button slideIn"
                                               id="details{{ $building['id'] }}"
                                               ref="{{ $building['id'] }}"
                                               href="javascript:void(0);">
            <span class="eckeoben">
                <span style="font-size:11px;" class="undermark"> {{ $building['current_level'] + 1 }}</span>
            </span>
            <span class="ecke">
                <span class="level">{{ $building['current_level'] }}</span>
            </span>
                                            </a>
                                        </div>
                                    @endif
                                    <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="{!! $building['title'] !!}" ref="{!! $building['id'] !!}" id="details" href="javascript:void(0);">
                        <span class="ecke">
                            <span class="level">
                               <span class="textlabel">
                                   {!! $building['title'] !!}
                               </span>
                                {!! $building['current_level'] !!}	                           </span>
                        </span>
                                    </a>
                                </div>
                            </div>
                        </li>
                @endforeach

                    <!--<li class="off">
                        <div class="item_box research106">
                            <div class="buildingimg">
                                <a href="javascript:void(0);" ref="106" id="details106" title="Espionage Technology<br/>No research laboratory" class="detail_button tooltip js_hideTipOnMobile slideIn">
                                <span class="ecke">
                                    <span class="level">
                                        <span class="textlabel">Espionage Technology </span>
                                        0                                        <span class="undermark">
                                                                                    </span>
                                    </span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="off">
                        <div class="item_box research108">
                            <div class="buildingimg">
                                <a href="javascript:void(0);" ref="108" id="details108" title="Computer Technology<br/>No research laboratory" class="detail_button tooltip js_hideTipOnMobile slideIn">
                                <span class="ecke">
                                    <span class="level">
                                        <span class="textlabel">Computer Technology </span>
                                        0                                        <span class="undermark">
                                                                                    </span>
                                    </span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="off">
                        <div class="item_box research124">
                            <div class="buildingimg">
                                <a href="javascript:void(0);" ref="124" id="details124" title="Astrophysics<br/>No research laboratory" class="detail_button tooltip js_hideTipOnMobile slideIn">
                                <span class="ecke">
                                    <span class="level">
                                        <span class="textlabel">Astrophysics </span>
                                        0                                        <span class="undermark">
                                                                                    </span>
                                    </span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="off">
                        <div class="item_box research123">
                            <div class="buildingimg">
                                <a href="javascript:void(0);" ref="123" id="details123" title="Intergalactic Research Network<br/>No research laboratory" class="detail_button tooltip js_hideTipOnMobile slideIn">
                                <span class="ecke">
                                    <span class="level">
                                        <span class="textlabel">Intergalactic Research Network </span>
                                        0                                        <span class="undermark">
                                                                                    </span>
                                    </span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="off">
                        <div class="item_box research199">
                            <div class="buildingimg">
                                <a href="javascript:void(0);" ref="199" id="details199" title="Graviton Technology<br/>No research laboratory" class="detail_button tooltip js_hideTipOnMobile slideIn">
                                <span class="ecke">
                                    <span class="level">
                                        <span class="textlabel">Graviton Technology </span>
                                        0                                        <span class="undermark">
                                                                                    </span>
                                    </span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </li>-->
                </ul>
            </div>        <div id="wrapBattle" class="resRight fleft">
                <h2>Combat research</h2>
                <ul id="base4" class="activate">
                    @foreach ($research[3] as $building)
                        <li class="@if ($building['currently_building'])
                                on
                            @elseif (!$building['requirements_met'])
                                off
                            @elseif (!$building['enough_resources'])
                                disabled
                            @elseif ($build_queue_max)
                                disabled
                            @else
                                on
                            @endif
                                ">
                            <div class="item_box research{!! $building['id'] !!}">
                                <div class="buildingimg">
                                    @if ($building['requirements_met'] && $building['enough_resources'])
                                        <a class="fastBuild tooltip js_hideTipOnMobile" title="Research {!! $building['title'] !!} level {!! ($building['current_level'] + 1) !!}" href="javascript:void(0);" onclick="sendBuildRequest('{!! route('research.addbuildrequest') !!}', null, 1);">
                                            <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="22" height="14">
                                        </a>
                                    @endif
                                    @if ($building['currently_building'])
                                        <div class="construction">
                                            <div class="pusher" id="b_research{{ $building['id'] }}" style="height:100px;">
                                            </div>
                                            <a class="slideIn timeLink" href="javascript:void(0);" ref="{{ $building['id'] }}">
                                                <span class="time" id="test" name="zeit"></span>
                                            </a>

                                            <a class="detail_button slideIn"
                                               id="details{{ $building['id'] }}"
                                               ref="{{ $building['id'] }}"
                                               href="javascript:void(0);">
            <span class="eckeoben">
                <span style="font-size:11px;" class="undermark"> {{ $building['current_level'] + 1 }}</span>
            </span>
            <span class="ecke">
                <span class="level">{{ $building['current_level'] }}</span>
            </span>
                                            </a>
                                        </div>
                                    @endif
                                    <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="{!! $building['title'] !!}" ref="{!! $building['id'] !!}" id="details" href="javascript:void(0);">
                        <span class="ecke">
                            <span class="level">
                               <span class="textlabel">
                                   {!! $building['title'] !!}
                               </span>
                                {!! $building['current_level'] !!}	                           </span>
                        </span>
                                    </a>
                                </div>
                            </div>
                        </li>
                @endforeach

                    <!--<li class="off">
                        <div class="item_box research109">
                            <div class="buildingimg">
                                <a href="javascript:void(0);" ref="109" id="details109" title="Weapons Technology<br/>No research laboratory" class="detail_button tooltip js_hideTipOnMobile slideIn">
                                <span class="ecke">
                                    <span class="level">
                                        <span class="textlabel">Weapons Technology </span>
                                        0                                        <span class="undermark">
                                                                                    </span>
                                    </span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="off">
                        <div class="item_box research110">
                            <div class="buildingimg">
                                <a href="javascript:void(0);" ref="110" id="details110" title="Shielding Technology<br/>No research laboratory" class="detail_button tooltip js_hideTipOnMobile slideIn">
                                <span class="ecke">
                                    <span class="level">
                                        <span class="textlabel">Shielding Technology </span>
                                        0                                        <span class="undermark">
                                                                                    </span>
                                    </span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="off">
                        <div class="item_box research111">
                            <div class="buildingimg">
                                <a href="javascript:void(0);" ref="111" id="details111" title="Armour Technology<br/>No research laboratory" class="detail_button tooltip js_hideTipOnMobile slideIn">
                                <span class="ecke">
                                    <span class="level">
                                        <span class="textlabel">Armour Technology </span>
                                        0                                        <span class="undermark">
                                                                                    </span>
                                    </span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </li>-->
                </ul>
            </div>        <br class="clearfloat">
        </div>    <div class="content-box-s">
            <div class="header"><h3>Research</h3></div>
            <div class="content">
                <table cellspacing="0" cellpadding="0" class="construction active">
                    <tbody>
                    {{-- Building is actively being built. --}}
                    @if (!empty($build_active['id']))
                        <tr>
                            <th colspan="2">{!! $build_active['object']['title'] !!}</th>
                        </tr>
                        <tr class="data">
                            <td class="first" rowspan="3">
                                <div>
                                    <a href="javascript:void(0);" class="tooltip js_hideTipOnMobile" style="display: block;" onclick="cancelProduction({!! $build_active['object']['id'] !!},{!! $build_active['id'] !!},&quot;Cancel expansion of {!! $build_active['object']['title'] !!} to level {!! $build_active['object']['level_target'] !!}?&quot;); return false;" title="">
                                        <img class="queuePic" width="40" height="40" src="{!! asset('img/objects/research/' . $build_active['object']['assets']['img']['small']) !!}" alt="{!! $build_active['object']['title'] !!}">
                                    </a>
                                    <a href="javascript:void(0);" class="tooltip abortNow js_hideTipOnMobile" onclick="cancelProduction({!! $build_active['object']['id'] !!},{!! $build_active['id'] !!},&quot;Cancel expansion of {!! $build_active['object']['title'] !!} to level {!! $build_active['object']['level_target'] !!}?&quot;); return false;" title="Cancel expansion of {!! $build_active['object']['title'] !!} to level {!! $build_active['object']['level_target'] !!}?">
                                        <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="15" width="15">
                                    </a>
                                </div>
                            </td>
                            <td class="desc ausbau">Improve to						<span class="level">Level {!! $build_active['object']['level_target'] !!}</span>
                            </td>
                        </tr>
                        <tr class="data">
                            <td class="desc">Duration:</td>
                        </tr>
                        <tr class="data">
                            <td class="desc timer">
                                <span id="Countdown">Loading...</span>
                                <!-- JAVASCRIPT -->
                                <script type="text/javascript">
                                    var timerHandler=new TimerHandler();
                                    new baulisteCountdown(getElementByIdWithCache("Countdown"), {!! $build_active['time_countdown'] !!}, "{!! route('research.index') !!}");
                                </script>
                            </td>
                        </tr>
                        <tr class="data">
                            <td colspan="2">
                                <a class="build-faster dark_highlight tooltipLeft js_hideTipOnMobile building disabled" title="Reduces construction time by 50% of the total construction time (15s)." href="javascript:void(0);" rel="{{ route('shop.index', ['buyAndActivate' => 'cb4fd53e61feced0d52cfc4c1ce383bad9c05f67']) }}">
                                    <div class="build-faster-img" alt="Halve time"></div>
                                    <span class="build-txt">Halve time</span>
                            <span class="dm_cost overmark">
                                Costs: 750 DM                            </span>
                                    <span class="order_dm">Purchase Dark Matter</span>
                                </a>
                            </td>
                        </tr>
                    @endif

                    {{-- Building queue has items. --}}
                    @if (count($build_queue) > 0)
                        <table class="queue">
                            <tbody><tr>
                                @foreach ($build_queue as $item)
                                    <td>
                                        <a href="javascript:void(0);" class="queue_link tooltip js_hideTipOnMobile dark_highlight_tablet" onclick="cancelProduction({!! $item['object']['id'] !!},{!! $item['id'] !!},&quot;Cancel expansion of {!! $item['object']['title'] !!} to level {!! $item['object']['level_target'] !!}?&quot;); return false;" title="">
                                            <img class="queuePic" src="{!! asset('img/objects/research/' . $item['object']['assets']['img']['micro']) !!}" height="28" width="28" alt="{!! $item['object']['title'] !!}">
                                            <span>{!! $item['object']['level_target'] !!}</span>
                                        </a>
                                    </td>
                                @endforeach
                            </tr>
                            </tbody></table>
                    @endif

                    {{-- No buildings are being built. --}}
                    @if (empty($build_active))
                        <tr>
                            <td colspan="2" class="idle">
                                <a class="tooltip js_hideTipOnMobile
                           " title="There is no research done at the moment. Click here to get to your research lab." href="{{ route('research.index') }}">
                                    There is no research in progress at the moment.
                                </a>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="footer"></div>
        </div>
    </div>

@endsection