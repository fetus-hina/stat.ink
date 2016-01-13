{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'Privacy Policy'|translate:'app'}}"}}
  <div class="container">
    <h1>
      {{'Privacy Policy'|translate:'app'}}
    </h1>

    {{AdWidget}}
    {{SnsWidget}}

    <p>
      {{'This website ({0}) collects your access data below:'|translate:'app':$app->name|escape}}
    </p>
    <ul>
      <li>
        {{'Access time'|translate:'app'|escape}}
      </li>
      <li>
        {{'Your IP address'|translate:'app'|escape}}
      </li>
      <li>
        {{'The address of the webpage that linked to our page being requested (aka "Referer")'|translate:'app'|escape}}
      </li>
      <li>
        {{'The OS and browser name/version that you used (aka "User-Agent")'|translate:'app'|escape}}
      </li>
    </ul>
    <p>
      {{'We use Cookies.'|translate:'app'|escape}}
    </p>
    <p>
      {{'We use the Google Analytics for analyzing users\' information.'|translate:'app'|escape}}
    </p>
    <p>
      {{'We don\'t exhibit your information that we collected (e.g. your IP address).'|translate:'app'|escape}}
      {{'However, statistics information will be opened.'|translate:'app'|escape}}
    </p>
    <p>
      {{'When there was a charge from investigating authority, your information would be elucidated.'|translate:'app'|escape}}
    </p>

    <h2 id="image">
      {{'About image sharing with the IkaLog team'|translate:'app'|escape}}
    </h2>
    <p>
      {{'Your uploaded data (battle data, images and modification histories) will share with the IkaLog development team.'|translate:'app'|escape}}
    </p>
    <p>
      {{'It\'s doing automatically and will not be deleted even after the deletion of the battle.'|translate:'app'|escape}}
    </p>
    <p>
      {{'This behaviour was started at 27 Oct 2015.'|translate:'app'|escape}}
    </p>
  </div>
{{/strip}}
