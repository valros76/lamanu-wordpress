window.addEventListener("DOMContentLoaded", (e) => {
  var meteo_form = document.getElementById('meteo-form')
  String.prototype.capitalize = function () {
    return this.charAt(0).toUpperCase() + this.slice(1);
  }

  let createWidget = (id_city) => {
    if (document.querySelector('#widget_preview')) {
      let widget = `<div id="openweathermap-widget-15"></div>`
      document.querySelector('#widget_preview').insertAdjacentHTML('beforeend', widget)

      window.myWidgetParam ? window.myWidgetParam : window.myWidgetParam = [];
      window.myWidgetParam.push({
        id: 15, cityid: `${id_city}`, appid: 'e849a1bb2385f437c9ab3ce45ea1a5a1', units: 'metric', containerid: 'openweathermap-widget-15',
      }); (function () {
        var script = document.createElement('script');
        script.async = true;
        script.src = "//openweathermap.org/themes/openweathermap/assets/vendor/owm/js/weather-widget-generator.js";
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(script, s);
      })();
      return widget
    }
  }

  meteo_form.addEventListener("submit", (e) => {
    e.preventDefault()
    let api_key = document.getElementById('api_key').value
    let city = document.getElementById('city_id').value
    var url;
    if (isNaN(city)) {
      url = `https://api.openweathermap.org/data/2.5/weather?q=${city.capitalize() + ',fr'}&appid=${api_key}&units=metric&lang=fr`
    } else {
      url = `https://api.openweathermap.org/data/2.5/weather?id=${parseInt(city)}&appid=${api_key}&units=metric&lang=fr`
    }


    var city_id;
    fetch(url, {
      method: 'POST',
      'action': 'meteo_widget'
    })
      .then(response => response.json())
      .then((response) => {
        if (isNaN(city)) {
          // alert(JSON.stringify(response.id))
          city_id = response.id
        } else {
          // alert(JSON.stringify(response))
          city_id = city
        }

        if (!document.querySelector('#openweathermap-widget-15')) {
          // document.querySelector('#short-code').textContent = createWidget(city_id)
          createWidget(city_id)
        }else{
          document.querySelector('#widget_preview').removeChild(document.querySelector('#openweathermap-widget-15'))
          createWidget(city_id)
        }
      })
      .catch(error => alert("Erreur : " + error))
  })
})