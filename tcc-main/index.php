<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previsão do Tempo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #a1c4fd, #c2e9fb);
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .weather-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }
        .weather-container h2 {
            margin: 0;
            color: #007bff;
        }
        .current-weather {
            margin: 20px 0;
        }
        .forecast {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        .forecast-day {
            background-color: #f0f0f0;
            border-radius: 10px;
            padding: 10px;
            width: 100px;
        }
        .forecast-day h4 {
            margin: 5px 0;
        }
        .forecast-day p {
            margin: 3px 0;
        }
        .pokemon-image {
            margin-top: 20px;
        }
        img {
            width: 150px;
            height: auto;
        }
        #myChart {
            max-width: 800px;
            height: 400px;
            margin: 20px auto;
        }
    </style>
</head>
<body>

<div class="weather-container">
    <h2 id="location">Previsão do Tempo</h2>
    <div class="current-weather">
        <h3>Clima Atual</h3>
        <p><strong>Temperatura:</strong> <span id="temp"></span>°C</p>
        <p><strong>Condição:</strong> <span id="condition"></span></p>
    </div>

    <canvas id="myChart"></canvas>

    <div class="forecast" id="forecast">
        <!-- A previsão para os próximos dias será inserida aqui -->
    </div>

    <div class="pokemon-image" id="pokemon-container">
        <!-- A imagem do Pokémon e seu nome será inserida aqui -->
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const latitude = -21.248833; // Latitude fornecida
    const longitude = -50.314750; // Longitude fornecida
    const apiKey = '63f855ca90e54238acf111640242308'; // Insira sua chave de API aqui

    const pokemonList = [
        { name: "Pikachu", id: 25 },
        { name: "Charmander", id: 4 },
        { name: "Squirtle", id: 7 },
        { name: "Bulbasaur", id: 1 },
        { name: "Jigglypuff", id: 39 },
        { name: "Eevee", id: 133 },
        { name: "Meowth", id: 52 },
        { name: "Snorlax", id: 143 },
        { name: "Mewtwo", id: 150 },
        { name: "Gengar", id: 94 },
        { name: "Lapras", id: 131 },
        { name: "Psyduck", id: 54 },
        { name: "Lucario", id: 448 },
        { name: "Charizard", id: 6 },
        { name: "Togepi", id: 175 },
        { name: "Dragonite", id: 149 },
        { name: "Gardevoir", id: 282 },
        { name: "Machamp", id: 68 },
        { name: "Arcanine", id: 59 },
        { name: "Alakazam", id: 65 },
        { name: "Onix", id: 95 },
        { name: "Mew", id: 151 },
        { name: "Ditto", id: 132 },
        { name: "Chikorita", id: 152 },
        { name: "Cyndaquil", id: 155 },
        { name: "Totodile", id: 158 },
        { name: "Togekiss", id: 468 },
        { name: "Lopunny", id: 428 },
        { name: "Shaymin", id: 492 },
        { name: "Grotle", id: 388 },
        { name: "Torterra", id: 389 },
        { name: "Piplup", id: 393 },
        { name: "Empoleon", id: 395 },
        { name: "Zoroark", id: 571 },
        { name: "Zubat", id: 41 },
        { name: "Pidgey", id: 16 },
        { name: "Pidgeotto", id: 17 },
        { name: "Pidgeot", id: 18 },
        { name: "Sandslash", id: 28 },
        { name: "Misdreavus", id: 200 },
        { name: "Crobat", id: 169 },
        { name: "Gligar", id: 207 },
        { name: "Surskit", id: 283 },
        { name: "Masquerain", id: 284 },
        { name: "Electivire", id: 466 },
        { name: "Roserade", id: 407 },
        { name: "Gallade", id: 475 },
        { name: "Froslass", id: 478 },
        { name: "Bisharp", id: 625 },
        { name: "Krookodile", id: 553 },
        { name: "Greninja", id: 658 }
    ];

    async function getWeatherData(lat, lon) {
        try {
            const weatherResponse = await fetch(`https://api.weatherapi.com/v1/forecast.json?key=${apiKey}&q=${lat},${lon}&days=5&lang=pt`);
            const weatherData = await weatherResponse.json();

            if (weatherResponse.status !== 200) {
                throw new Error(weatherData.error.message);
            }

            // Atualiza a interface com os dados do tempo
            document.getElementById('location').innerText = `Previsão do Tempo para ${weatherData.location.name}`;
            document.getElementById('temp').innerText = Math.round(weatherData.current.temp_c);
            document.getElementById('condition').innerText = weatherData.current.condition.text;

            let forecastContainer = document.getElementById('forecast');
            forecastContainer.innerHTML = ''; // Limpa o conteúdo anterior

            // Dados para os gráficos
            const maxTemps = [];
            const minTemps = [];
            const rainChances = [];
            const labels = [];

            // Previsão para os próximos dias
            weatherData.forecast.forecastday.forEach(day => {
                let forecastDay = document.createElement('div');
                forecastDay.className = 'forecast-day';
                forecastDay.innerHTML = `
                    <h4>${new Date(day.date).toLocaleDateString('pt-BR', { weekday: 'short', day: 'numeric', month: 'short' })}</h4>
                    <p><strong>Max:</strong> ${Math.round(day.day.maxtemp_c)}°C</p>
                    <p><strong>Min:</strong> ${Math.round(day.day.mintemp_c)}°C</p>
                    <p>${day.day.condition.text}</p>
                    <p><strong>Precipitação:</strong> ${day.day.totalprecip_mm} mm</p>
                    <p><strong>Chance de Chuva:</strong> ${day.day.daily_chance_of_rain} %</p>
                `;
                forecastContainer.appendChild(forecastDay);

                // Adiciona os dados para os gráficos
                maxTemps.push(Math.round(day.day.maxtemp_c));
                minTemps.push(Math.round(day.day.mintemp_c));
                rainChances.push(day.day.daily_chance_of_rain);
                labels.push(new Date(day.date).toLocaleDateString('pt-BR', { weekday: 'short', day: 'numeric', month: 'short' }));
            });

            // Cria os gráficos
            createChart(labels, maxTemps, minTemps, rainChances);

            // Adiciona um Pokémon aleatório
            const randomPokemon = pokemonList[Math.floor(Math.random() * pokemonList.length)];
            document.getElementById('pokemon-container').innerHTML = `
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${randomPokemon.id}.png" alt="${randomPokemon.name}">
                <p>${randomPokemon.name}</p>
            `;
        } catch (error) {
            alert('Erro ao acessar a WeatherAPI: ' + error.message);
            console.error(error);
        }
    }

    function createChart(labels, maxTemps, minTemps, rainChances) {
        const ctx = document.getElementById('myChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Temperatura Máxima (°C)',
                        data: maxTemps,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Temperatura Mínima (°C)',
                        data: minTemps,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Chance de Chuva (%)',
                        data: rainChances,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        stacked: false
                    }
                }
            }
        });
    }

    getWeatherData(latitude, longitude);
</script>

</body>
</html>