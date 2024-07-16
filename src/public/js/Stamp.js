document.addEventListener('DOMContentLoaded', function () {
    // 最初に正しいボタン状態を設定
    resetButtonsToInitialState();

    // 以下のイベントリスナーと既存の関数はそのまま
    document.getElementById('clock_in').addEventListener('click', function () {
        clock_in();
        sendRequest('clock_in');
    });

    document.getElementById('clock_out').addEventListener('click', function () {
        clock_out();
        sendRequest('clock_out');
    });

    document.getElementById('rest_start').addEventListener('click', function () {
        rest_start();
        sendRequest('rest_start');
    });

    document.getElementById('rest_end').addEventListener('click', function () {
        rest_end();
        sendRequest('rest_end');
    });
});

function resetButtonsToInitialState() {
    // 最初に勤務開始のみを有効にし、他は無効に設定
    document.getElementById('clock_in').disabled = false;
    document.getElementById('clock_out').disabled = true;
    document.getElementById('rest_start').disabled = true;
    document.getElementById('rest_end').disabled = true;

    document.getElementById('clock_in').classList.add('btn__on');
    document.getElementById('clock_out').classList.add('btn__off');
    document.getElementById('rest_start').classList.add('btn__off');
    document.getElementById('rest_end').classList.add('btn__off');
}


function clock_in() {
    document.getElementById('clock_in').disabled = true;
    document.getElementById('clock_in').classList.remove('btn__on');
    document.getElementById('clock_in').classList.add('btn__off');

    enableButtons(['clock_out', 'rest_start']);
}

function clock_out() {
    disableAllButtons();
}

function rest_start() {
    document.getElementById('rest_start').disabled = true;
    document.getElementById('rest_start').classList.remove('btn__on');
    document.getElementById('rest_start').classList.add('btn__off');

    enableButtons(['rest_end']);
}

function rest_end() {
    document.getElementById('rest_end').disabled = true;
    document.getElementById('rest_end').classList.remove('btn__on');
    document.getElementById('rest_end').classList.add('btn__off');

    document.getElementById('rest_start').disabled = false;
    document.getElementById('rest_start').classList.remove('btn__off');
    document.getElementById('rest_start').classList.add('btn__on');
}

function enableButtons(buttonIds) {
    buttonIds.forEach(function (id) {
        document.getElementById(id).disabled = false;
        document.getElementById(id).classList.remove('btn__off');
        document.getElementById(id).classList.add('btn__on');
    });
}

function disableAllButtons() {
    const buttons = ['clock_in', 'clock_out', 'rest_start', 'rest_end'];
    buttons.forEach(function (id) {
        document.getElementById(id).disabled = true;
        document.getElementById(id).classList.remove('btn__on');
        document.getElementById(id).classList.add('btn__off');
    });
}

function sendRequest(action) {
    fetch(`/stamp/${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response:', data);  // デバッグのためにレスポンスをログに出力
    })
    .catch(error => console.error('Error:', error));
}
