{{strip}}
{{set layout="asis.php"}}
<?xml version="1.0" encoding="UTF-8" ?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:thr="http://purl.org/syndication/thread/1.0" xmlns:activity="http://activitystrea.ms/spec/1.0/" xmlns:poco="http://portablecontacts.net/spec/1.0" xmlns:media="http://purl.org/syndication/atommedia" xmlns:ostatus="http://ostatus.org/schema/1.0" xmlns:mastodon="http://mastodon.social/schema/1.0">
  <id>{{url route="/ostatus/feed" screen_name=$user->screen_name}}</id>
  <title>{{$user->screen_name|escape}}</title>
  <subtitle></subtitle>
  <updated>{{$smarty.now|date_format:'Y-m-d\TH:i:sP':'':'php'|escape}}</updated>
  <logo>{{if $user->userIcon}}{{$user->userIcon->absUrl|escape}}{{else}}{{$user->jdenticonPngUrl|escape}}{{/if}}</logo>
  <author>
    <id>{{url route="/show/user" screen_name=$user->screen_name}}</id>
    <activity:object-type>http://activitystrea.ms/schema/1.0/person</activity:object-type>
    <uri>{{url route="/show/user" screen_name=$user->screen_name}}</uri>
    <name>{{$user->name|escape}}</name>
    <email>{{$user->screen_name|escape}}@{{\Yii::$app->request->hostName|escape}}</email>
    <summary></summary>
    <link rel="alternate" type="text/html" href="{{url route="/show/user" screen_name=$user->screen_name}}" />
    <link rel="avatar" type="" media:width="120" media:height="120" href="{{if $user->userIcon}}{{$user->userIcon->absUrl|escape}}{{else}}{{$user->jdenticonPngUrl|escape}}{{/if}}" />
    <link rel="header" type="" media:width="700" media:height="335" href="/headers/original/missing.png" />
    <poco:preferredUsername>{{$user->screen_name|escape}}</poco:preferredUsername>
    <mastodon:scope>public</mastodon:scope>
  </author>
  <link rel="alternate" type="text/html" href="{{url route="/show/user" screen_name=$user->screen_name}}" />
  <link rel="self" type="application/atom+xml" href="{{url route="/ostatus/feed" screen_name=$user->screen_name}}" />
  <link rel="hub" href="{{url route="/ostatus/pubsubhubbub"}}" />
  <link rel="salmon" href="{{url route="/ostatus/salmon" screen_name=$user->screen_name}}" />
  {{$_battles = $user->getBattles()->with(['map', 'rule'])->orderBy('id DESC')->limit(50)->all()}}
  {{foreach $_battles as $_battle}}
    <entry>
      <id>{{url route="/show/battle" screen_name=$user->screen_name battle=$_battle->id}}</id>
      <published>{{$_battle->at|date_format:'Y-m-d\TH:i:sP':'':'php'|escape}}</published>
      <updated>{{$_battle->at|date_format:'Y-m-d\TH:i:sP':'':'php'|escape}}</updated>
      <title>New battle by {{$user->screen_name|escape}}</title>
      <activity:object-type>http://activitystrea.ms/schema/1.0/note</activity:object-type>
      <activity:verb>http://activitystrea.ms/schema/1.0/post</activity:verb>
      <content type="html" xml:lang="ja">
        {{capture _content}}
          <p>
            {{$_battle->map->name|default:'スプラトゥーン'|translate:'app-map'|escape}}での
            {{$_battle->rule->name|default:'バトル'|translate:'app-rule'|escape}}
            {{if $_battle->is_win === null}}
              を戦いました
            {{elseif $_battle->is_win}}
              に勝ちました
            {{else}}
              に負けました
            {{/if}} [
            {{if $_battle->kill === null}}??{{else}}{{$_battle->kill|escape}}{{/if}}k/
            {{if $_battle->death === null}}??{{else}}{{$_battle->death|escape}}{{/if}}d
            ] #IkaLogResult
          </p>
        {{/capture}}
        {{$smarty.capture._content}}
      </content>
      <link rel="mentioned" ostatus:object-type="http://activitystrea.ms/schema/1.0/collection" href="http://activityschema.org/collection/public" />
      <mastodon:scope>public</mastodon:scope>
      <link rel="alternate" type="text/html" href="{{url route="/show/battle" battle=$_battle->id screen_name=$user->screen_name}}" />
      <link rel="self" type="application/atom+xml" href="{{url route="/ostatus/battle-atom" battle=$_battle->id screen_name=$user->screen_name}}" />
    </entry>
  {{/foreach}}
</feed>
{{/strip}}
