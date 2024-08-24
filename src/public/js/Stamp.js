document.addEventListener('DOMContentLoaded', function () {
    // 最初にボタンの状態をリセット
    resetButtonsToInitialState();

    // 勤務開始が既に押されているか確認
    checkClockInStatus();

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

function checkClockInStatus() {
    fetch('/stamp/check-status')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'already_clocked_in') {
                clock_in();
            }
        })
        .catch(error => console.error('Error:', error));
}

function resetButtonsToInitialState() {
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
        console.log('Response:', data);
    })
    .catch(error => console.error('Error:', error));
}

function resetButtonsToInitialState() {
    // 日付のリセット処理
    let lastAccessDate = localStorage.getItem('lastAccessDate');
    let today = new Date().toISOString().split('T')[0];

    if (lastAccessDate !== today) {
        localStorage.setItem('lastAccessDate', today);
        localStorage.removeItem('clockedIn');  // 勤務開始ボタンの状態をリセット
    }

    // 勤務開始ボタンの状態をチェック
    let clockedIn = localStorage.getItem('clockedIn');
    if (!clockedIn) {
        document.getElementById('clock_in').disabled = false;
        document.getElementById('clock_in').classList.add('btn__on');
    } else {
        clock_in();  // 勤務開始済みの状態にする
    }
}

function clock_out() {
    let restStart = document.getElementById('rest_start').disabled;
    if (restStart && !document.getElementById('rest_end').disabled) {
        document.getElementById('rest_end').click();
    }
    disableAllButtons();
    sendRequest('clock_out');
}


