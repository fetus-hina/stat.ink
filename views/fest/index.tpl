{{strip}}
<div class="container">
  <h1 class="ikamodoki">
    イカフェスレート
  </h1>
  <p>
    スプラトゥーンの公式サイトで公開されているデータを基にフェスの勝率を推定するサイトです。
  </p>
  <table class="table table-stripe" id="fest-list">
    <tbody>
      {{foreach $allFest as $_fest}}
        <tr>
          <td>
            {{if $_fest->id == 1}}
              <a href="{{path route="/fest/view" id=$_fest->id}}" class="btn btn-default ikamodoki auto-tooltip" title="データの取得を行っていないため何も表示されません">
                みる
              </a>
            {{else}}
              <a href="{{path route="/fest/view" id=$_fest->id}}" class="btn btn-primary ikamodoki">
                みる
              </a>
            {{/if}}
          </td>
          <td>
            第{{$_fest->id|escape}}回
          </td>
          <td>
            {{$_fest->name|escape}}
          </td>
          <td>
            <span class="fest-term-begin">
              <span class="fest-term-date">{{$_fest->start_at|date_format:'%Y-%m-%d'|escape}}</span>&#32;
              <span class="fest-term-time">{{$_fest->start_at|date_format:'%H:%M %Z'|escape}}</span>
            </span> <span class="fest-term-range">～</span> <span class="fest-term-end">
              <span class="fest-term-date">{{$_fest->end_at|date_format:'%Y-%m-%d'|escape}}</span>&#32;
              <span class="fest-term-time">{{$_fest->end_at|date_format:'%H:%M %Z'|escape}}</span>
            </span>
          </td>
        </tr>
      {{/foreach}}
    </tbody>
  </table>
  <p>
    ※第1回分はページは作ってありますが集計していないので何も表示されません。
  </p>

  {{include '@app/views/fest/attention.tpl'}}
</div>
{{/strip}}
