# IntersectionObserver コンポーネント（slot版）

Astroプロジェクト用の汎用的なIntersectionObserverコンポーネントです。slotを使って、要素が画面に入ったときに `data-active="true"` 属性を設定します。

## 基本的な使用方法

### 1. コンポーネントをインポートしてslotで包む

```astro
---
import IntersectionObserver from '../components/IntersectionObserver.astro';
---

<IntersectionObserver class="opacity-0 translate-y-8 transition-all duration-700 data-[active=true]:opacity-100 data-[active=true]:translate-y-0">
  <div>
    この要素は画面に入るとアニメーションします
  </div>
</IntersectionObserver>
```

## プロパティ

| プロパティ | 型 | デフォルト値 | 説明 |
|-----------|---|------------|------|
| `threshold` | number | `0.1` | 交差の閾値 (0.0 - 1.0) |
| `rootMargin` | string | `'0px'` | ルートマージン |
| `once` | boolean | `true` | 一度だけ実行するかどうか |
| `class` | string | `''` | 追加のクラス名 |

## Tailwind CSSを使った自由なアニメーション

`data-[active=true]:` セレクターを使って、Tailwindユーティリティクラスで自由にアニメーションを作成できます：

### フェードイン
```astro
<IntersectionObserver class="opacity-0 transition-opacity duration-700 data-[active=true]:opacity-100">
  <div>フェードイン要素</div>
</IntersectionObserver>
```

### 下からスライドイン
```astro
<IntersectionObserver class="opacity-0 translate-y-8 transition-all duration-700 data-[active=true]:opacity-100 data-[active=true]:translate-y-0">
  <div>下からスライドイン</div>
</IntersectionObserver>
```

### 左からスライドイン
```astro
<IntersectionObserver class="opacity-0 -translate-x-8 transition-all duration-700 data-[active=true]:opacity-100 data-[active=true]:translate-x-0">
  <div>左からスライドイン</div>
</IntersectionObserver>
```

### 右からスライドイン
```astro
<IntersectionObserver class="opacity-0 translate-x-8 transition-all duration-700 data-[active=true]:opacity-100 data-[active=true]:translate-x-0">
  <div>右からスライドイン</div>
</IntersectionObserver>
```

### スケールアニメーション
```astro
<IntersectionObserver class="opacity-0 scale-95 transition-all duration-700 data-[active=true]:opacity-100 data-[active=true]:scale-100">
  <div>スケールアニメーション</div>
</IntersectionObserver>
```

### 回転アニメーション
```astro
<IntersectionObserver class="opacity-0 rotate-12 transition-all duration-700 data-[active=true]:opacity-100 data-[active=true]:rotate-0">
  <div>回転アニメーション</div>
</IntersectionObserver>
```

### 複合アニメーション
```astro
<IntersectionObserver class="opacity-0 translate-y-8 scale-95 rotate-3 transition-all duration-700 ease-out data-[active=true]:opacity-100 data-[active=true]:translate-y-0 data-[active=true]:scale-100 data-[active=true]:rotate-0">
  <div>複合アニメーション</div>
</IntersectionObserver>
```

## 使用例

### カスタム設定
```astro
<!-- 50%見えたときにトリガー -->
<IntersectionObserver 
  threshold={0.5}
  class="opacity-0 transition-opacity duration-1000 data-[active=true]:opacity-100"
>
  <div>50%で表示される要素</div>
</IntersectionObserver>
```

### 繰り返し実行
```astro
<!-- 要素が画面に入る/出るたびにトリガー -->
<IntersectionObserver 
  once={false}
  class="opacity-0 transition-opacity duration-500 data-[active=true]:opacity-100"
>
  <div>繰り返し実行される要素</div>
</IntersectionObserver>
```

### 子要素の個別監視
```astro
<IntersectionObserver>
  <div>
    <div data-observe class="opacity-0 transition-opacity duration-700 data-[active=true]:opacity-100">子要素1</div>
    <div data-observe class="opacity-0 transition-opacity duration-700 data-[active=true]:opacity-100">子要素2</div>
  </div>
</IntersectionObserver>
```

## Problems.astroでの実装例

```astro
---
import IntersectionObserver from './IntersectionObserver.astro';
---

<section class="problems-section">
  <IntersectionObserver class="opacity-0 translate-y-8 transition-all duration-700 ease-out data-[active=true]:opacity-100 data-[active=true]:translate-y-0">
    <h2>セクションタイトル</h2>
  </IntersectionObserver>
  
  <div class="grid">
    <IntersectionObserver class="opacity-0 -translate-x-8 transition-all duration-700 ease-out data-[active=true]:opacity-100 data-[active=true]:translate-x-0">
      <article>記事1</article>
    </IntersectionObserver>
    
    <IntersectionObserver class="opacity-0 translate-x-8 transition-all duration-700 ease-out data-[active=true]:opacity-100 data-[active=true]:translate-x-0">
      <article>記事2</article>
    </IntersectionObserver>
    
    <IntersectionObserver class="opacity-0 scale-95 transition-all duration-700 ease-out data-[active=true]:opacity-100 data-[active=true]:scale-100">
      <article>記事3</article>
    </IntersectionObserver>
  </div>
</section>
```

## 利点

1. **Tailwindユーティリティクラスの活用**: CSSを書かずにアニメーションを実装
2. **柔軟性**: 任意のプロパティを組み合わせて独自のアニメーションを作成
3. **再利用性**: slotを使ってどんな要素でも包める
4. **型安全**: Astroのpropsで型チェックされた設定
5. **パフォーマンス**: 必要な要素のみを監視

## 注意事項

1. **ブラウザサポート**: IntersectionObserver APIは現代的なブラウザでサポート
2. **アクセシビリティ**: `prefers-reduced-motion`を考慮することを推奨
3. **パフォーマンス**: 大量の要素を監視する場合は適切な`threshold`設定を
