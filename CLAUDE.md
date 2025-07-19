# プロジェクト情報

このファイルは、GOALS21リニューアルサイトのAstroプロジェクトに関する重要な情報を記載しています。

## プロジェクト概要

- **プロジェクト名**: goals21_new
- **フレームワーク**: Astro 5.12.0
- **スタイリング**: Tailwind CSS (Vite版 4.1.11)
- **パッケージマネージャー**: Bunを使用

## 基本コマンド

### 開発サーバー
```bash
bun run dev
```

### ビルド
```bash
bun run build
```

### プレビュー
```bash
bun run preview
```

### コードフォーマット
```bash
bun run format       # フォーマット実行
bun run format:check # フォーマットチェック
```

## プロジェクト構造

```
src/
├── components/      # コンポーネント（Astroファイル）
│   ├── Header.astro
│   ├── Hero.astro
│   ├── Benefits.astro
│   ├── CaseStudies.astro
│   ├── Comparison.astro
│   ├── Contact.astro
│   ├── Coverage.astro
│   ├── Difficulties.astro
│   ├── Pricing.astro
│   ├── Problems.astro
│   ├── ServiceDetails.astro
│   ├── SupportSystem.astro
│   └── Urgency.astro
├── layouts/
│   └── Layout.astro  # ベースレイアウト
├── pages/
│   └── index.astro   # ホームページ
├── assets/          # 静的アセット
├── scripts/         # JavaScriptファイル
└── styles/
    └── global.css   # グローバルスタイル
```

## 開発ガイドライン

### コードスタイル
- Prettierを使用してコードフォーマットを統一
- Astroコンポーネントには prettier-plugin-astro を適用
- Tailwind CSSには prettier-plugin-tailwindcss を適用

### コンポーネント開発
- 各セクションは独立したAstroコンポーネントとして実装
- レスポンシブデザインはTailwind CSSのユーティリティクラスで対応
- Layout.astroをベースレイアウトとして使用

## 特記事項

- GOALS21リニューアルサイト用のランディングページ
- モダンなAstroフレームワークとTailwind CSSを採用
- 各種コンポーネントが細かく分割されており、保守性を重視した設計

## 応答ルール

**重要**: Claude Codeによる回答は必ず日本語で行ってください。技術的な説明、コード解説、エラーメッセージの説明など、すべてのコミュニケーションを日本語で実施してください。