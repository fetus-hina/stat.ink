{{strip}}
  {{\app\assets\BlackoutHintAsset::register($this)|@void}}
  <div class="table-responsive">
    <table id="blackout-info" class="table table-bordered table-condensed">
      <thead>
        <tr>
          <th>
            {{'Solo Queue'|translate:'app-rule'|escape}}
            <!--
            <br>
            {{'Splatfest'|translate:'app-rule'|escape}}
            -->
          </th>
          <th>
            {{'Squad Battle (Twin)'|translate:'app-rule'|escape}}
          </th>
          <th>
            {{'Squad Battle (Tri)'|translate:'app-rule'|escape}}<br>
            {{'Squad Battle (Quad)'|translate:'app-rule'|escape}}
          </th>
          <th>
            {{'Private Battle'|translate:'app-rule'|escape}}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          {{$_modes = ['standard', 'squad_2', 'squad_4', 'private']}}
          {{$_cats = [
              'user'      => Yii::t('app', 'You'),
              'good-guys' => Yii::t('app', 'Good Guys'),
              'bad-guys'  => Yii::t('app', 'Bad Guys')
            ]}}
          {{foreach $_modes as $_mode}}
            <td>
              {{foreach $_cats as $_cat => $_catName}}
                {{if !$_catName@first}}
                  <br>
                {{/if}}
                <span class="blackout-info-icon fa fa-fw fa-square-o" data-mode="{{$_mode|escape}}" data-category="{{$_cat|escape}}"></span>
                {{$_catName|escape}}
              {{/foreach}}
            </td>
          {{/foreach}}
        </tr>
      </tbody>
    </table>
    <p id="blackout-info-legends">
      {{'Legends'|translate:'app'|escape}}:&#32;
      <span class="fa fa-fw fa-square-o"></span>{{'No black out'|translate:'app'|escape}}
      &#32;/&#32;
      <span class="fa fa-fw fa-check-square-o"></span>{{'Black out'|translate:'app'|escape}}
    </p>
  </div>
{{/strip}}
