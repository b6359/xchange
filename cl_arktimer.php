<?
class ArkTimer
{
    var $start;

    function ArkTimer()
    {
        $this->start = $this->GetMicrotime();
    }

    function GetMicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    function GetTime()
    {
        return round($this->GetMicrotime() - $this->start, 6);
    }

    function EchoTime()
    {
        echo "<hr size=1>Realizuar ne ";
        echo round($this->GetMicrotime() - $this->start, 6);
        echo " sekonda";
    }

    function ReturnTime()
    {
        $fin =  round($this->GetMicrotime() - $this->start, 6);
        return $fin;
    }
}

?>


<script>
    // Disable right-click context menu
    document.addEventListener('contextmenu', event => event.preventDefault());
</script>

<script>
    // Disable keyCode
    document.addEventListener('keydown', e => {
        if
        // Disable F1
        (e.keyCode === 112 ||

            // Disable F3
            e.keyCode === 114 ||

            // Disable F5
            e.keyCode === 116 ||

            // Disable F6
            e.keyCode === 117 ||

            // Disable F7
            e.keyCode === 118 ||

            // Disable F10
            e.keyCode === 121 ||

            // Disable F11
            e.keyCode === 122 ||

            // Disable F12
            e.keyCode === 123 ||

            // Disable Ctrl
            e.ctrlKey ||

            // Disable Shift
            e.shiftKey ||

            // Disable Alt
            e.altKey ||

            // Disable Ctrl+Shift+Key
            e.ctrlKey && e.shiftKey ||

            // Disable Ctrl+Shift+alt
            e.ctrlKey && e.shiftKey && e.altKey
        ) {
            e.preventDefault();
            //alert('Not Allowed');
        }
    });
</script>