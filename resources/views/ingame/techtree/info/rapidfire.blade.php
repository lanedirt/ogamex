@if (!empty($rapidfire_against) || !empty($rapidfire_from))
    <ul class="rapid_fire">
        @foreach ($rapidfire_from as $from => $value)
            <li class="rapid_fire_from {{ $value['object']['class_name'] }}">
                Rapidfire from
                <a href="#TODO_page=ajax&amp;component=technologytree&amp;ajax=1&amp;technologyId={{ $from }}&amp;tab=2" class="overlay" data-overlay-same="true">{{ $value['object']['title'] }}</a>:
                <span class="value" data-value="{{ $value['amount'] }}">
                    @php
                        // Assuming $value['chance'] is your float number
                        $chance = $value['chance'];

                        // Determine the correct number of decimal places
                        if (floor($chance) == $chance) {
                            // It's a whole number, no need for decimals
                            $formattedChance = number_format($chance, 0);
                        } else {
                            // It's a decimal number, check for how many significant digits are needed
                            $roundedChance = round($chance, 2);
                            if (round($roundedChance, 1) == $roundedChance) {
                                // If rounding to 1 decimal gives the same result, we need only 1 decimal
                                $formattedChance = number_format($roundedChance, 1);
                            } else {
                                // Otherwise, use 2 decimals
                                $formattedChance = number_format($roundedChance, 2);
                            }
                        }

                        // Format the amount normally since it's likely an integer
                        $formattedAmount = number_format($value['amount']);
                    @endphp
                    {{ $formattedChance }}% ({{ $formattedAmount }})
                </span>
            </li>
        @endforeach

        @foreach ($rapidfire_against as $target => $value)
            <li class="rapid_fire_against {{ $value['object']['class_name'] }}">
                Rapidfire against
                <a href="#TODO_page=ajax&amp;component=technologytree&amp;ajax=1&amp;technologyId={{ $target }}&amp;tab=2" class="overlay" data-overlay-same="true">{{ $value['object']['title'] }}</a>:
                <span class="value" data-value="{{ $value['amount'] }}">
                    @php
                        // Assuming $value['chance'] is your float number
                        $chance = $value['chance'];

                        // Determine the correct number of decimal places
                        if (floor($chance) == $chance) {
                            // It's a whole number, no need for decimals
                            $formattedChance = number_format($chance, 0);
                        } else {
                            // It's a decimal number, check for how many significant digits are needed
                            $roundedChance = round($chance, 2);
                            if (round($roundedChance, 1) == $roundedChance) {
                                // If rounding to 1 decimal gives the same result, we need only 1 decimal
                                $formattedChance = number_format($roundedChance, 1);
                            } else {
                                // Otherwise, use 2 decimals
                                $formattedChance = number_format($roundedChance, 2);
                            }
                        }

                        // Format the amount normally since it's likely an integer
                        $formattedAmount = number_format($value['amount']);
                    @endphp
                    {{ $formattedChance }}% ({{ $formattedAmount }})
                </span>
        @endforeach
    </ul>
@endif