import { defineAction } from 'astro:actions';
import { z } from 'astro:schema';

export const server = {
  // 他のサーバーアクションがあればここに追加
  sendContactForm: defineAction({
    accept: 'form',
    input: z.object({
      name: z.string().min(1, '名前を入力してください'),
      email: z.string().email('有効なメールアドレスを入力してください'),
      phone: z.string().optional(),
      subject: z.string().min(1, '件名を入力してください'),
      message: z.string().min(10, 'メッセージは10文字以上で入力してください'),
      inquiry_type: z.enum(
        ['general', 'account_closure', 'procedures', 'consultation'],
        {
          errorMap: () => ({ message: 'お問い合わせ種別を選択してください' }),
        }
      ),
    }),
    handler: async input => {
      // ここで実際のメール送信処理などを行う
      // 現在はコンソールログとして出力
      console.log('お問い合わせを受信しました:', input);

      // 実際の実装では以下のような処理を行う:
      // - メール送信サービス（SendGrid, Nodemailer等）の使用
      // - データベースへの保存
      // - 管理者への通知

      try {
        // フォーム送信の模擬処理
        await new Promise(resolve => setTimeout(resolve, 1000));

        return {
          success: true,
          message:
            'お問い合わせありがとうございます。担当者より2営業日以内にご連絡いたします。',
        };
      } catch (error) {
        console.error('お問い合わせ処理エラー:', error);
        throw new Error(
          'お問い合わせの送信に失敗しました。しばらく時間をおいて再度お試しください。'
        );
      }
    },
  }),
};
