<div>
    <div id="days" class="">
        <table class="table-auto border-separate ">
            <tr>
                @php
                    $date = strtotime("+6 day");
                    for($j = 0; $j <=6; $j++){
                        $date = strtotime("$j day");
                    echo "<td class='hover:select-all hover:text-2xl'>". date('d', $date)."</td>";
                    }
                @endphp
                @foreach($chosen_services as $service)
                {{$service}}<br>
                @endforeach
                {{$chosen_employee}}

            </tr>
        </table>




    </div>

</div>
