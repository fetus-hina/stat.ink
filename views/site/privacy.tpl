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
      {{'This website ({0}) collects the following data:'|translate:'app':$app->name|escape}}
    </p>
    <ul>
      <li>
        {{'Access time'|translate:'app'|escape}}
      </li>
      <li>
        {{'IP address'|translate:'app'|escape}}
      </li>
      <li>
        {{'The address of the web site that linked here (aka "referer")'|translate:'app'|escape}}
      </li>
      <li>
        {{'Your OS, browser name, and version that you are using (aka "user agent")'|translate:'app'|escape}}
      </li>
    </ul>
    <p>
      {{'This site uses cookies, as well as Google Analytics for tracking user information.'|translate:'app'|escape}}
    </p>
    <p>
      {{'We don\'t release your collected information, like your IP address. However, statistical information will be released.'|translate:'app'|escape}}
    </p>
    <p>
      {{'If there is an investigation being conducted by the police or other authority, your information will be released.'|translate:'app'|escape}}
    </p>

    <h2 id="image">
      {{'About image sharing with the IkaLog team'|translate:'app'|escape}}
    </h2>
    <p>
      {{'Your uploaded data (battle stats, images, and modification history) will be shared with the IkaLog development team.'|translate:'app'|escape}}
    </p>
    <p>
      {{'This is done automatically and the data will not be deleted even if the the battle is deleted.'|translate:'app'|escape}}
    </p>
    <p>
      {{'This behavior was started on 27 Oct 2015.'|translate:'app'|escape}}
    </p>
  </div>
{{/strip}}
