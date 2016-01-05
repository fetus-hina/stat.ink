{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | About Translation"}}

  {{\app\assets\AboutAsset::register($this)|@void}}

  <div class="container" lang="en">
    <h1>
      About Translation
    </h1>

    {{AdWidget}}
    {{SnsWidget}}

    <h2>
      As Of Today
    </h2>
    <p>
      This website supports Japanese and English. But, there are some pages that has not been translated. And there is a lot of my bad English. Sorry for inconvinience.
    </p>

    <h2>
      Need Help
    </h2>
    <p>
      I'm looking for translation and/or proofreading volunteer staff. Please contact me if you help me.
    </p>
  </div>
{{/strip}}
