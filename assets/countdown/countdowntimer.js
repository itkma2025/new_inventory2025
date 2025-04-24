$.fn.setCountDownTimer = function (param) {
    this.html('<label class="countdownText" style="float:left;margin-right:5px;">' + param.countDownText + '</label>' +
            '<div id="countdown" style="float:left;">' +
                '<strong>' +
                    '<span id="minutes" style="float:left"></span>' +
                    '<span style="float:left">:</span>' +
                    '<span id="seconds" style="float:left"></span>' +
                '</strong>' +
            '</div>' +
            '<div id="aftercount" style="display:none">' + param.afterCountText + '</div>');
    var remTime = param.time.split(":"),
            sTime = new Date().getTime(),
            countDown = (remTime[01] * 60) + parseInt(remTime[02]),
            minutesSel = $("#minutes"),
            secondsSel = $("#seconds"),
            afterCountSel = $("#aftercount"),
            countDownSel = $("#countdown"),
            countDownTextSel = $('.countdownText');

    var timer = setInterval(function () {
        var cTime = new Date().getTime(),
        diff = cTime - sTime,
        seconds = countDown - Math.floor(diff / 1000);
        param.button.attr('disabled', 'disabled');
        if (seconds >= 0) {
            var minutes = Math.floor(seconds / 60);
            seconds -= minutes * 60;
            minutesSel.text(minutes < 10 ? "0" + minutes : minutes);
            secondsSel.text(seconds < 10 ? "0" + seconds : seconds);
        } else {
            countDownSel.hide();
            countDownTextSel.hide();
            afterCountSel.show();
            param.button.removeAttr('disabled');
            clearInterval(timer);
        }
    }, 500);
}