{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'What\'s this?'|translate:'app'}}"}}
  <div class="container">
    <h1>
      {{'What\'s this?'|translate:'app'}}
    </h1>
    <p>
      このサイトは、スプラトゥーンの勝敗データを自動的に収集して統計として後で解析できるように蓄積するサイトです。
    </p>
    <p>
      まだ作り始めたばかりなのでほとんど何もできませんが、将来的にはグラフによる勝率変化の遷移などの様々な情報解析ができるようになる……つもりです。
    </p>
    <h2>
      使い方
    </h2>
    <p>
      まず、利用するには <a href="https://github.com/hasegaw/IkaLog/blob/master/doc/IkaUI.md">IkaLog</a> などの解析ソフトと、キャプチャデバイス（キャプチャボード）等が必要です。
    </p>
    <p>
      キャプチャデバイスの準備と、IkaLog (等)の対応ソフトを用意できれば、あとはこのサイトでユーザ登録をして、発行される「APIキー」を対応ソフトに登録すれば自動的にデータが収集されます。
    </p>
    <p>
      このサイトの中の人は AVT-C875 というキャプチャデバイスをつかっています。実際の使い方は <a href="http://mzsm.me/2015/09/23/hdmi-capture-avt-c875/">mzsmさん（イカデンワの中の人）の紹介・解説記事</a>が参考になります。
    </p>
    <div>
      <iframe src="//rcm-fe.amazon-adsystem.com/e/cm?lt1=_blank&amp;bc1=FFFFFF&amp;IS1=1&amp;bg1=FFFFFF&amp;fc1=000000&amp;lc1=0000FF&amp;t=statink-22&amp;o=9&amp;p=8&amp;l=as1&amp;m=amazon&amp;f=ifr&amp;ref=qf_sp_asin_til&amp;asins=B00EU7Y9OW" style="width:120px;height:240px;" scrolling="no" marginwidth="0" marginheight="0" frameborder="0"></iframe>
    </div>
    <h3>
      高度な使い方
    </h3>
    <p>
      stat.ink は <a href="https://github.com/fetus-hina/stat.ink/blob/master/API.md">API を公開しています</a>ので、IkaLog に限らず、例えば自作のソフトウェアによっても登録が行えます。
    </p>
  </div>
{{/strip}}
