# IntersectionObserver コンポーネント

Astroプロジェクト用の汎用的なIntersectionObserverコンポーネントです。slotを使って、要素が画面に入ったときに `data-observer="true"` 属性を設定します。

## 概要

このコンポーネントは2つの使用方法をサポートします：

1. **slot版（推奨）**: コンポーネントで要素を包み、Tailwind CSSクラスで自由にアニメーションを定義
2. **クラス版**: 従来のCSS クラスベースのアニメーション

## 基本的な使用方法（slot版・推奨）

### 1. コンポーネントをインポートしてslotで包む

```astro
---
import IntersectionObserver from '../components/IntersectionObserver.astro';
---

<IntersectionObserver
  class="translate-y-8 opacity-0 transition-all duration-700 data-[observer=true]:translate-y-0 data-[observer=true]:opacity-100"
>
  <div>この要素は画面に入るとアニメーションします</div>
</IntersectionObserver>
```

## プロパティ

| プロパティ   | 型      | デフォルト値 | 説明                           |
| ------------ | ------- | ------------ | ------------------------------ |
| `threshold`  | number  | `0.1`        | 交差の閾値 (0.0 - 1.0)         |
| `rootMargin` | string  | `'0px'`      | ルートマージン                 |
| `once`       | boolean | `true`       | 一度だけ実行するかどうか       |
| `class`      | string  | `''`         | 追加のクラス名（slot版で使用） |

## Tailwind CSSを使った自由なアニメーション（slot版）

`data-[observer=true]:` セレクターを使って、Tailwindユーティリティクラスで自由にアニメーションを作成できます：

### フェードイン

```astro
<IntersectionObserver
  class="opacity-0 transition-opacity duration-700 data-[observer=true]:opacity-100"
>
  <div>フェードイン要素</div>
</IntersectionObserver>
```

### 下からスライドイン

```astro
<IntersectionObserver
  class="translate-y-8 opacity-0 transition-all duration-700 data-[observer=true]:translate-y-0 data-[observer=true]:opacity-100"
>
  <div>下からスライドイン</div>
</IntersectionObserver>
```

### 左からスライドイン

```astro
<IntersectionObserver
  class="-translate-x-8 opacity-0 transition-all duration-700 data-[observer=true]:translate-x-0 data-[observer=true]:opacity-100"
>
  <div>左からスライドイン</div>
</IntersectionObserver>
```

### 右からスライドイン

```astro
<IntersectionObserver
  class="translate-x-8 opacity-0 transition-all duration-700 data-[observer=true]:translate-x-0 data-[observer=true]:opacity-100"
>
  <div>右からスライドイン</div>
</IntersectionObserver>
```

### スケールアニメーション

```astro
<IntersectionObserver
  class="scale-95 opacity-0 transition-all duration-700 data-[observer=true]:scale-100 data-[observer=true]:opacity-100"
>
  <div>スケールアニメーション</div>
</IntersectionObserver>
```

### 回転アニメーション

```astro
<IntersectionObserver
  class="rotate-12 opacity-0 transition-all duration-700 data-[observer=true]:rotate-0 data-[observer=true]:opacity-100"
>
  <div>回転アニメーション</div>
</IntersectionObserver>
```

### 複合アニメーション

```astro
<IntersectionObserver
  class="translate-y-8 scale-95 rotate-3 opacity-0 transition-all duration-700 ease-out data-[observer=true]:translate-y-0 data-[observer=true]:scale-100 data-[observer=true]:rotate-0 data-[observer=true]:opacity-100"
>
  <div>複合アニメーション</div>
</IntersectionObserver>
```

## 使用例（slot版）

### カスタム設定

```astro
<!-- 50%見えたときにトリガー -->
<IntersectionObserver
  threshold={0.5}
  class="opacity-0 transition-opacity duration-1000 data-[observer=true]:opacity-100"
>
  <div>50%で表示される要素</div>
</IntersectionObserver>
```

### 繰り返し実行

```astro
<!-- 要素が画面に入る/出るたびにトリガー -->
<IntersectionObserver
  once={false}
  class="opacity-0 transition-opacity duration-500 data-[observer=true]:opacity-100"
>
  <div>繰り返し実行される要素</div>
</IntersectionObserver>
```

### 子要素の個別監視

```astro
<IntersectionObserver>
  <div>
    <div
      data-observe
      class="opacity-0 transition-opacity duration-700 data-[observer=true]:opacity-100"
    >
      子要素1
    </div>
    <div
      data-observe
      class="opacity-0 transition-opacity duration-700 data-[observer=true]:opacity-100"
    >
      子要素2
    </div>
  </div>
</IntersectionObserver>
```

## Problems.astroでの実装例（slot版）

```astro
---
import IntersectionObserver from './IntersectionObserver.astro';
---

<section class="problems-section">
  <IntersectionObserver
    class="translate-y-8 opacity-0 transition-all duration-700 ease-out data-[observer=true]:translate-y-0 data-[observer=true]:opacity-100"
  >
    <h2>セクションタイトル</h2>
  </IntersectionObserver>

  <div class="grid">
    <IntersectionObserver
      class="-translate-x-8 opacity-0 transition-all duration-700 ease-out data-[observer=true]:translate-x-0 data-[observer=true]:opacity-100"
    >
      <article>記事1</article>
    </IntersectionObserver>

    <IntersectionObserver
      class="translate-x-8 opacity-0 transition-all duration-700 ease-out data-[observer=true]:translate-x-0 data-[observer=true]:opacity-100"
    >
      <article>記事2</article>
    </IntersectionObserver>

    <IntersectionObserver
      class="scale-95 opacity-0 transition-all duration-700 ease-out data-[observer=true]:scale-100 data-[observer=true]:opacity-100"
    >
      <article>記事3</article>
    </IntersectionObserver>
  </div>
</section>
```

## 従来のクラス版の使用方法（レガシー）

### 1. レイアウトまたはページにコンポーネントをインポート

```astro
---
import IntersectionObserver from '../components/IntersectionObserver.astro';
---

<html>
  <head>
    <!-- ... -->
  </head>
  <body>
    <!-- コンテンツ -->
    <div class="observe-element">
      この要素は画面に入るとアニメーションします
    </div>

    <!-- IntersectionObserverコンポーネントを配置 -->
    <IntersectionObserver />
  </body>
</html>
```

### 2. CSSクラスを適用

基本的な使用方法では、監視したい要素に `observe-element` クラスを追加するだけです：

```html
<div class="observe-element">アニメーションする要素</div>
```

### クラス版のプロパティ

| プロパティ    | 型      | デフォルト値        | 説明                     |
| ------------- | ------- | ------------------- | ------------------------ |
| `targetClass` | string  | `'observe-element'` | 監視する要素のクラス名   |
| `threshold`   | number  | `0.1`               | 交差の閾値 (0.0 - 1.0)   |
| `rootMargin`  | string  | `'0px'`             | ルートマージン           |
| `once`        | boolean | `true`              | 一度だけ実行するかどうか |

### クラス版の使用例

#### カスタムクラスを使用

```astro
<IntersectionObserver targetClass="my-custom-element" />

<div class="my-custom-element">カスタムクラスで監視される要素</div>
```

#### 閾値を調整

```astro
<!-- 要素の50%が見えたときにトリガー -->
<IntersectionObserver threshold={0.5} />
```

#### 繰り返し実行

```astro
<!-- 要素が画面に入る/出るたびにトリガー -->
<IntersectionObserver once={false} />
```

#### ルートマージンの設定

```astro
<!-- 要素が画面に入る100px前にトリガー -->
<IntersectionObserver rootMargin="100px" />
```

### 利用可能なCSSクラス（クラス版）

global.cssに以下のクラスが定義されています：

#### 基本クラス

- `.observe-element` - 下からフェードイン
- `.observe-fade` - フェードインのみ
- `.observe-scale` - スケールアニメーション

#### スライドイン

- `.observe-slide-left` - 左からスライドイン
- `.observe-slide-right` - 右からスライドイン

### カスタムCSSの追加（クラス版）

独自のアニメーションを作成する場合：

```css
.my-custom-animation {
  opacity: 0;
  transform: rotate(-10deg) scale(0.8);
  transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.my-custom-animation[data-observer='true'] {
  opacity: 1;
  transform: rotate(0deg) scale(1);
}
```

## slot版の利点

1. **Tailwindユーティリティクラスの活用**: CSSを書かずにアニメーションを実装
2. **柔軟性**: 任意のプロパティを組み合わせて独自のアニメーションを作成
3. **再利用性**: slotを使ってどんな要素でも包める
4. **型安全**: Astroのpropsで型チェックされた設定
5. **パフォーマンス**: 必要な要素のみを監視
6. **保守性**: クラス名の管理が不要

## 注意事項

1. **パフォーマンス**: 大量の要素を監視する場合は、`threshold`や`rootMargin`を適切に設定してください
2. **アクセシビリティ**: `prefers-reduced-motion`メディアクエリを考慮したスタイルを追加することを推奨します
3. **ブラウザサポート**: IntersectionObserver APIは現代的なブラウザでサポートされています

## トラブルシューティング

### アニメーションが動作しない場合

1. `IntersectionObserver`コンポーネントがページに配置されているか確認
2. 対象要素に正しいクラス名が設定されているか確認（クラス版の場合）
3. ブラウザの開発者ツールで`data-observer`属性が変更されているか確認
4. Tailwindクラスが正しく記述されているか確認（slot版の場合）
