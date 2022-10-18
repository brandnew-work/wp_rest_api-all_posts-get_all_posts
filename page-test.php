<?php get_header(); ?>

<main>
  <article>
    <div id="archive">
      <archive
        :post-fetch-url="'/wp-json/wp/v2/posts'"
        :per-page="10"
        :post-query="{
          '_fields': 'id,content,title,categories,_links,_embedded',
          '_embed': '',
        }"
        #post="{ data }"
        >
        <span v-text="data.id"></span>
      </archive>
    </div>
  </article>
</main>

<?php get_footer(); ?>
