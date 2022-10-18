<template>
  <div class="my-post-archive">
    <article class="archive" v-if="datas">
      <section class="archive__item" v-for="data in datas" :key="data.id">
        <slot :data="data" name="post" />
      </section>
    </article>
  </div>
</template>

<style lang="scss" scoped>
.archive {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: .5rem 1rem;
  margin-top: 1rem;
  &__item {
    width: calc((100% / 3) - ( 1rem * (3 - 1) ));
    text-align: center;
  }
}
</style>

<script>
  import axios from 'axios'

  export default {
    props: {
      postFetchUrl: {
        type: String,
        require: true
      },
      postQuery: {
        type: Object,
        default: () => ({
          '_embed':   '',  // アイキャッチ情報の取得
        })
      },
      perPage: {
        type: Number,
        default: 10,
      },
    },
    data() {
      return {
        dataOrigin:    [],
        dataBase:      [],
        totalPages:    0,
        totalPosts:    0,
        pager:         1,
      }
    },
    mounted() {
      this.getPostDatas()
    },
    methods: {

      /* post archive ------------------------------------------------- */

        // 投稿の取得
        async getPostData(num, postQuery = this.postQuery) {

          // パラメータを追加できるようにしておく
          let insertParams = []                           // 送信するパラメータの宣言
          const params = { page: num }                    // ページの指定
          insertParams = await {...postQuery, ...params}  // postQueryとparamsを結合

          // 投稿を取得してdataOriginに追加
          await axios.get(this.postFetchUrl, {params: insertParams})
          .then((res) => {
            this.dataOrigin = this.dataOrigin.concat(res.data)  // データをdataOriginに結合
            this.dataBase   = this.dataOrigin                   // 範囲指定などがあった場合のための保存用データ
          })
          .catch(error => console.log(error))
        },

        // 投稿のheader情報取得
        async getPostInfo() {
          await axios.get(this.postFetchUrl)
          .then((res) => {
            this.totalPages = Number(res.headers['x-wp-totalpages']) // 合計ページ数
            this.totalPosts = Number(res.headers['x-wp-total'])      // 合計投稿数
          })
          .catch(error => console.log(error))
        },

        // 投稿の全件取得
        async getPostDatas() {

          // 投稿のheader情報取得
          await this.getPostInfo()

          // 投稿のページ数分の配列を作成
          const numPostAry = [...Array(this.totalPages).keys()].map( i => ++i )

          // 投稿のページ数分ループを回す
          for (let num of numPostAry) {
            await this.getPostData(num)
          }
        },

    },
    computed: {

      // 表示dataの取得
      datas() {
        return this.dataBase.slice(0, this.perPage)
      },

    },
  }
</script>
