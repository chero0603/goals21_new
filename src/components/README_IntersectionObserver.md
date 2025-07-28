# IntersectionObserver コンポーネント

Astroプロジェクト用の汎用的なIntersectionObserverコンポーネントです。要素が画面に入ったときに `data-active="true"` 属性を設定します。

## 基本的な使用方法

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
<div class="observe-element">
  アニメーションする要素
</div>
```

## プロパティ

| プロパティ | 型 | デフォルト値 | 説明 |
|-----------|---|------------|------|
| `targetClass` | string | `'observe-element'` | 監視する要素のクラス名 |
| `threshold` | number | `0.1` | 交差の閾値 (0.0 - 1.0) |
| `rootMargin` | string | `'0px'` | ルートマージン |
| `once` | boolean | `true` | 一度だけ実行するかどうか |

## 使用例

### カスタムクラスを使用

```astro
<IntersectionObserver targetClass="my-custom-element" />

<div class="my-custom-element">
  カスタムクラスで監視される要素
</div>
```

### 閾値を調整

```astro
<!-- 要素の50%が見えたときにトリガー -->
<IntersectionObserver threshold={0.5} />
```

### 繰り返し実行

```astro
<!-- 要素が画面に入る/出るたびにトリガー -->
<IntersectionObserver once={false} />
```

### ルートマージンの設定

```astro
<!-- 要素が画面に入る100px前にトリガー -->
<IntersectionObserver rootMargin="100px" />
```

## 利用可能なCSSクラス

global.cssに以下のクラスが定義されています：

### 基本クラス
- `.observe-element` - 下からフェードイン
- `.observe-fade` - フェードインのみ
- `.observe-scale` - スケールアニメーション

### スライドイン
- `.observe-slide-left` - 左からスライドイン
- `.observe-slide-right` - 右からスライドイン

## 実装例

### Problems.astroでの使用例

```astro
---
import IntersectionObserver from './IntersectionObserver.astro';
---

<section class="problems-section">
  <h2 class="observe-element">セクションタイトル</h2>
  
  <div class="grid">
    <article class="observe-slide-left">記事1</article>
    <article class="observe-slide-right">記事2</article>
    <article class="observe-scale">記事3</article>
  </div>
</section>

<!-- IntersectionObserverを配置 -->
<IntersectionObserver 
  targetClass="observe-element"
  threshold={0.1}
  once={true}
/>

<!-- 複数のクラスを監視する場合 -->
<IntersectionObserver targetClass="observe-slide-left" />
<IntersectionObserver targetClass="observe-slide-right" />
<IntersectionObserver targetClass="observe-scale" />
```

### カスタムCSSの追加

独自のアニメーションを作成する場合：

```css
.my-custom-animation {
  opacity: 0;
  transform: rotate(-10deg) scale(0.8);
  transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.my-custom-animation[data-active="true"] {
  opacity: 1;
  transform: rotate(0deg) scale(1);
}
```

## 注意事項

1. **パフォーマンス**: 大量の要素を監視する場合は、`threshold`や`rootMargin`を適切に設定してください
2. **アクセシビリティ**: `prefers-reduced-motion`メディアクエリを考慮したスタイルを追加することを推奨します
3. **ブラウザサポート**: IntersectionObserver APIは現代的なブラウザでサポートされています

## トラブルシューティング

### アニメーションが動作しない場合

1. `IntersectionObserver`コンポーネントがページに配置されているか確認
2. 対象要素に正しいクラス名が設定されているか確認
3. ブラウザの開発者ツールで`data-active`属性が変更されているか確認
