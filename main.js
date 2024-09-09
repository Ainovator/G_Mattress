document.addEventListener('DOMContentLoaded', function () {
    // Находим кнопку расчёта
    const calculateBtn = document.getElementById('calculate-btn');

    // Добавляем обработчик события "click" на кнопку
    calculateBtn.addEventListener('click', function () {
        // Получаем значения из полей ввода
        const width = parseInt(document.getElementById('input-mattress-width').value, 10);
        const length = parseInt(document.getElementById('input-mattress-length').value, 10);
        const bold = parseInt(document.getElementById('input-mattress-bold').value, 10);
        const quantity = parseInt(document.getElementById('input-mattress-amount').value, 10);
        const TextileCost = parseInt(document.getElementById('input-textile-cost').value, 10);
        const WorkTime = parseInt(document.getElementById('input-work-time').value, 10);
     


        // Получаем выбранные материалы и толщины для каждого слоя
        const materialFirstLayer = document.getElementById('material-first-layer').value;
        const boldFirstLayer = document.getElementById('bold-first-layer').value;
        const materialSecondLayer = document.getElementById('material-second-layer').value;
        const boldSecondLayer = document.getElementById('bold-second-layer').value;
        const materialThirdLayer = document.getElementById('material-third-layer').value;
        const boldThirdLayer = document.getElementById('bold-third-layer').value;

        // Определяем, какие карточки выбраны
        const isNoBortSelected = document.getElementById('card-no-bort').classList.contains('selected') ? 1 : 0;
        const isWithBortSelected = document.getElementById('card-with-bort').classList.contains('selected') ? 1 : 0;
        const isNoCantSelected = document.getElementById('card-no-cant').classList.contains('selected') ? 1 : 0;
        const isWithCantSelected = document.getElementById('card-with-cant').classList.contains('selected') ? 1 : 0;
        const isZipperSideSelected = document.getElementById('card-zipper-side').classList.contains('selected') ? 1 : 0;
        const isZipperBottomSelected = document.getElementById('card-zipper-bottom').classList.contains('selected') ? 1 : 0;
        const isUnoStitchSelected = document.getElementById('card-uno-stitch').classList.contains('selected') ? 1 : 0;
        const isDoubleStitchSelected = document.getElementById('card-double-stitch').classList.contains('selected') ? 1 : 0;
        const isWithPugSelected = document.getElementById('card-with-pug').classList.contains('selected') ? 1 : 0;
        const isPikSelected = document.getElementById('card-pik').classList.contains('selected') ? 1 : 0;


        const pugOnLength = parseInt(document.getElementById('length-step-slider').value, 10);
        console.log('Пуговицы по длине: ', pugOnLength);
        const pugOnWidth = parseInt(document.getElementById('width-step-slider').value, 10);
        console.log('Пуговицы по ширине: ', pugOnLength);
        const doublePug = document.getElementById('double-pug').checked ? 1 : 0;
        console.log('Пуговицы с двух сторон: ', doublePug);

        // Добавляем выбранные карточки в запрос
        const formCards = document.querySelectorAll('.form-card-container .form-card');
        const selectedCards = [];
        formCards.forEach(card => {
            if (card.classList.contains('selected')) {
                selectedCards.push(card.id); // Добавляем ID выбранных карточек в массив
            }
        });

        // Создаём объект для отправки
        const data = new URLSearchParams();
        data.append('width', width);
        data.append('length', length);
        data.append('bold', bold);
        data.append('quantity', quantity);
        data.append('textile-cost', TextileCost);
        data.append('work-time', WorkTime)
        data.append('material_first_layer', materialFirstLayer);
        data.append('bold_first_layer', boldFirstLayer);
        data.append('material_second_layer', materialSecondLayer);
        data.append('bold_second_layer', boldSecondLayer);
        data.append('material_third_layer', materialThirdLayer);
        data.append('bold_third_layer', boldThirdLayer);
        data.append('no_bort', isNoBortSelected);
        data.append('with_bort', isWithBortSelected);
        data.append('no_cant', isNoCantSelected);
        data.append('with_cant', isWithCantSelected);
        data.append('zipper_side', isZipperSideSelected);
        data.append('zipper_bottom', isZipperBottomSelected);
        data.append('uno_stitch', isUnoStitchSelected);
        data.append('double_stitch', isDoubleStitchSelected);
        data.append('with_pug', isWithPugSelected);
        data.append('pik', isPikSelected);
        data.append('selected_cards', JSON.stringify(selectedCards));
        data.append('pugOnLength', pugOnLength);
        data.append('pugOnWidth', pugOnWidth);
        data.append('doublePug', doublePug);

     
  
        // Отправляем AJAX-запрос на server.php
        fetch('server.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: data
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // Обработка успешного ответа и вывод результата в консоль
                console.log('Результат расчета:', result);
                console.log('Стоимость пуговиц', result['pug-cost']);
                document.getElementById('final-price-value').textContent = `${result['final-cost']} ₽`;
            } else {
                console.error('Ошибка:', result.message);
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
        });
    });
});
