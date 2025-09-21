/**
 * フォームバリデーション用のJavaScriptモジュール
 */

// バリデーション関数
const validators = {
  // 必須フィールドのチェック
  required: value => {
    return value.trim().length > 0;
  },

  // メールアドレスのバリデーション
  email: value => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(value);
  },

  // 電話番号のバリデーション（日本形式）
  phone: value => {
    const phoneRegex =
      /^(0[1-9]\d{0,3}-?\d{1,4}-?\d{4}|050-?\d{4}-?\d{4}|070-?\d{4}-?\d{4}|080-?\d{4}-?\d{4}|090-?\d{4}-?\d{4})$/;
    return phoneRegex.test(value.replace(/[\s\-]/g, ''));
  },

  // 最小文字数チェック
  minLength: (value, min) => {
    return value.trim().length >= min;
  },

  // 最大文字数チェック
  maxLength: (value, max) => {
    return value.trim().length <= max;
  },
};

// エラーメッセージ
const errorMessages = {
  required: 'この項目は必須です',
  email: '正しいメールアドレスを入力してください',
  phone: '正しい電話番号を入力してください',
  minLength: min => `${min}文字以上で入力してください`,
  maxLength: max => `${max}文字以内で入力してください`,
  name: {
    required: 'お名前は必須です',
    maxLength: 'お名前は50文字以内で入力してください',
  },
  email: {
    required: 'メールアドレスは必須です',
    email: '正しいメールアドレスを入力してください',
  },
  phone: {
    required: '電話番号は必須です',
    phone: '正しい電話番号を入力してください',
  },
  subject: {
    required: '件名は必須です',
    maxLength: '件名は100文字以内で入力してください',
  },
  message: {
    required: 'お問い合わせ内容は必須です',
    minLength: 'お問い合わせ内容は10文字以上で入力してください',
    maxLength: 'お問い合わせ内容は2000文字以内で入力してください',
  },
  inquiry_type: {
    required: 'お問い合わせ種別を選択してください',
  },
};

// バリデーションルール
const validationRules = {
  name: [{ type: 'required' }, { type: 'maxLength', value: 50 }],
  email: [{ type: 'required' }, { type: 'email' }],
  phone: [{ type: 'required' }, { type: 'phone' }],
  subject: [{ type: 'required' }, { type: 'maxLength', value: 100 }],
  message: [
    { type: 'required' },
    { type: 'minLength', value: 10 },
    { type: 'maxLength', value: 2000 },
  ],
  inquiry_type: [{ type: 'required' }],
};

// フィールドのバリデーション
function validateField(fieldName, value) {
  const rules = validationRules[fieldName];
  if (!rules) return { isValid: true, errors: [] };

  const errors = [];

  for (const rule of rules) {
    let isValid = false;

    switch (rule.type) {
      case 'required':
        isValid = validators.required(value);
        break;
      case 'email':
        isValid = !value || validators.email(value); // 空の場合はrequiredでチェック
        break;
      case 'phone':
        isValid = !value || validators.phone(value);
        break;
      case 'minLength':
        isValid = !value || validators.minLength(value, rule.value);
        break;
      case 'maxLength':
        isValid = validators.maxLength(value, rule.value);
        break;
    }

    if (!isValid) {
      const fieldMessages = errorMessages[fieldName];
      if (fieldMessages && fieldMessages[rule.type]) {
        errors.push(fieldMessages[rule.type]);
      } else if (typeof errorMessages[rule.type] === 'function') {
        errors.push(errorMessages[rule.type](rule.value));
      } else {
        errors.push(errorMessages[rule.type] || 'エラーが発生しました');
      }
    }
  }

  return {
    isValid: errors.length === 0,
    errors,
  };
}

// エラー表示
function showFieldError(fieldName, errors) {
  const field = document.getElementById(fieldName);
  if (!field) return;

  // 既存のエラーメッセージを削除
  removeFieldError(fieldName);

  if (errors.length > 0) {
    // フィールドにエラークラスを追加
    field.classList.add('border-red-500', 'ring-red-500');
    field.classList.remove('border-slate-300');

    // エラーメッセージを表示
    const errorDiv = document.createElement('div');
    errorDiv.className = 'mt-1 text-sm text-red-600';
    errorDiv.setAttribute('id', `${fieldName}-error`);
    errorDiv.textContent = errors[0]; // 最初のエラーメッセージのみ表示

    field.parentNode.appendChild(errorDiv);
  }
}

// エラー表示を削除
function removeFieldError(fieldName) {
  const field = document.getElementById(fieldName);
  const errorDiv = document.getElementById(`${fieldName}-error`);

  if (field) {
    field.classList.remove('border-red-500', 'ring-red-500');
    field.classList.add('border-slate-300');
  }

  if (errorDiv) {
    errorDiv.remove();
  }
}

// すべてのエラーを削除
function clearAllErrors() {
  Object.keys(validationRules).forEach(fieldName => {
    removeFieldError(fieldName);
  });
}

// フォーム全体のバリデーション
function validateForm(formData) {
  const results = {};
  let isFormValid = true;

  Object.keys(validationRules).forEach(fieldName => {
    const value = formData.get(fieldName) || '';
    const result = validateField(fieldName, value);
    results[fieldName] = result;

    if (!result.isValid) {
      isFormValid = false;
    }
  });

  return {
    isValid: isFormValid,
    results,
  };
}

// 文字数カウンター
function updateCharacterCount(fieldName, maxLength) {
  const field = document.getElementById(fieldName);
  const counterId = `${fieldName}-count`;

  if (!field) return;

  let counter = document.getElementById(counterId);
  if (!counter) {
    counter = document.createElement('div');
    counter.id = counterId;
    counter.className = 'mt-1 text-sm text-slate-500 text-right';
    field.parentNode.appendChild(counter);
  }

  const currentLength = field.value.length;
  counter.textContent = `${currentLength}/${maxLength}文字`;

  if (currentLength > maxLength) {
    counter.classList.add('text-red-600');
    counter.classList.remove('text-slate-500');
  } else {
    counter.classList.remove('text-red-600');
    counter.classList.add('text-slate-500');
  }
}

// リアルタイムバリデーション
function setupRealtimeValidation() {
  Object.keys(validationRules).forEach(fieldName => {
    const field = document.getElementById(fieldName);
    if (!field) return;

    // 文字数カウンターの設定
    const maxLengthRule = validationRules[fieldName].find(
      rule => rule.type === 'maxLength'
    );
    if (maxLengthRule) {
      field.addEventListener('input', () => {
        updateCharacterCount(fieldName, maxLengthRule.value);
      });
      // 初期表示
      updateCharacterCount(fieldName, maxLengthRule.value);
    }

    // バリデーション
    field.addEventListener('blur', () => {
      const value = field.value;
      const result = validateField(fieldName, value);

      if (!result.isValid) {
        showFieldError(fieldName, result.errors);
      } else {
        removeFieldError(fieldName);
      }
    });

    // 入力中はエラーを一時的に非表示
    field.addEventListener('input', () => {
      if (field.classList.contains('border-red-500')) {
        removeFieldError(fieldName);
      }
    });
  });
}

// 通知メッセージ表示（拡張版）
function showNotification(message, type = 'success', duration = 5000) {
  // 既存の通知の位置を調整
  const existingNotifications = document.querySelectorAll('.notification');
  existingNotifications.forEach((notification, index) => {
    const currentBottom = parseInt(notification.style.bottom) || 32;
    notification.style.bottom = `${currentBottom + 80}px`;
    notification.style.transition = 'all 0.3s ease-in-out';
  });

  const notification = document.createElement('div');
  notification.className = `notification fixed left-1/2 transform -translate-x-1/2 z-50 max-w-md w-11/12 sm:w-auto rounded-lg border px-6 py-4 shadow-xl transition-all duration-500 translate-y-full`;
  notification.style.bottom = '32px'; // 初期位置

  // アイコンを追加
  let icon = '';
  if (type === 'success') {
    notification.classList.add(
      'bg-green-50',
      'border-green-200',
      'text-green-800'
    );
    icon = '✅';
  } else if (type === 'error') {
    notification.classList.add('bg-red-50', 'border-red-200', 'text-red-800');
    icon = '❌';
  } else if (type === 'warning') {
    notification.classList.add(
      'bg-yellow-50',
      'border-yellow-200',
      'text-yellow-800'
    );
    icon = '⚠️';
  } else {
    notification.classList.add(
      'bg-blue-50',
      'border-blue-200',
      'text-blue-800'
    );
    icon = '💡';
  }

  notification.innerHTML = `
    <div class="flex items-start space-x-3">
      <div class="flex-shrink-0 text-xl">${icon}</div>
      <div class="flex-1 min-w-0">
        <div class="text-sm font-medium leading-relaxed text-center sm:text-left">${message}</div>
      </div>
      <button class="flex-shrink-0 ml-2 text-current opacity-70 hover:opacity-100 focus:outline-none transition-opacity" onclick="closeNotification(this.parentElement.parentElement)">
        <span class="sr-only">閉じる</span>
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
        </svg>
      </button>
    </div>
  `;

  document.body.appendChild(notification);

  // アニメーション（下から上にスライドイン）
  setTimeout(() => {
    notification.classList.remove('translate-y-full');
  }, 100);

  // 自動削除（下にスライドアウト）
  setTimeout(() => {
    if (notification.parentNode) {
      notification.classList.add('translate-y-full');
      setTimeout(() => {
        notification.remove();
        // 残った通知の位置を再調整
        adjustNotificationPositions();
      }, 500);
    }
  }, duration);
}

// 通知の位置を再調整する関数
function adjustNotificationPositions() {
  const notifications = document.querySelectorAll('.notification');
  notifications.forEach((notification, index) => {
    notification.style.bottom = `${32 + index * 80}px`;
    notification.style.transition = 'all 0.3s ease-in-out';
  });
}

// 通知を閉じる関数
function closeNotification(notification) {
  notification.classList.add('translate-y-full');
  setTimeout(() => {
    notification.remove();
    adjustNotificationPositions();
  }, 500);
}

// グローバルスコープに関数を追加
window.closeNotification = closeNotification;

// フォームの送信処理
async function handleFormSubmit(form) {
  const formData = new FormData(form);

  // バリデーション
  const validation = validateForm(formData);

  // エラー表示
  Object.keys(validation.results).forEach(fieldName => {
    const result = validation.results[fieldName];
    if (!result.isValid) {
      showFieldError(fieldName, result.errors);
    } else {
      removeFieldError(fieldName);
    }
  });

  if (!validation.isValid) {
    // エラーのある項目数をカウント
    const errorCount = Object.values(validation.results).filter(
      result => !result.isValid
    ).length;
    const errorMessage = `入力内容をご確認ください（${errorCount}件のエラー）`;

    showNotification(errorMessage, 'warning', 6000);

    // 最初のエラーフィールドにフォーカス
    const firstErrorField = Object.keys(validation.results).find(
      fieldName => !validation.results[fieldName].isValid
    );
    if (firstErrorField) {
      const field = document.getElementById(firstErrorField);
      if (field) {
        field.focus();
        field.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }

    return false;
  }

  // 送信ボタンの無効化とローディング表示
  const submitButton = form.querySelector('button[type="submit"]');
  const originalText = submitButton.textContent;
  submitButton.disabled = true;
  submitButton.classList.add('opacity-75', 'cursor-not-allowed');
  submitButton.innerHTML = `
    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    送信中...
  `;

  // 送信開始の通知
  showNotification('お問い合わせを送信しています...', 'info', 2000);

  try {
    // 開発環境と本番環境での処理分岐
    const isLocalhost =
      window.location.hostname.includes('localhost') ||
      window.location.hostname.includes('127.0.0.1');

    let result;

    if (isLocalhost) {
      // 開発環境：モックレスポンス
      console.log('Development mode: Using mock response');
      console.log('Form data:', Object.fromEntries(formData));

      // 少し遅延を入れてリアルな体験を提供
      await new Promise(resolve => setTimeout(resolve, 1000));

      result = {
        success: true,
        message:
          '【開発環境】お問い合わせを受け付けました。ありがとうございます。実際のメール送信は本番環境でのみ行われます。',
      };
    } else {
      // 本番環境：実際のPHPスクリプトに送信
      console.log('Production mode: Submitting to PHP script');

      // タイムスタンプとIPアドレスを追加
      formData.append('timestamp', new Date().toISOString());
      formData.append('client_ip', 'browser');

      const phpUrl = window.location.pathname.includes('/lp/')
        ? '/lp/mail.php'
        : '/mail.php';

      const response = await fetch(phpUrl, {
        method: 'POST',
        body: formData,
      });

      // レスポンスの処理
      const contentType = response.headers.get('content-type');

      if (contentType && contentType.includes('application/json')) {
        result = await response.json();
      } else {
        // PHPスクリプトからのレスポンス（JSON形式でない場合）
        const text = await response.text();
        console.log('PHP Response:', text);

        try {
          result = JSON.parse(text);
        } catch (e) {
          // JSONパースに失敗した場合
          if (response.ok) {
            result = {
              success: true,
              message: 'お問い合わせを受け付けました。ありがとうございます。',
            };
          } else {
            console.error('PHP script error:', text);
            result = {
              success: false,
              message:
                'メール送信に失敗しました。時間をおいて再度お試しください。',
            };
          }
        }
      }
    }

    if (result.success) {
      // 成功時の処理
      showNotification(result.message, 'success', 8000);
      form.reset();
      clearAllErrors();

      // 文字数カウンターもリセット
      Object.keys(validationRules).forEach(fieldName => {
        const maxLengthRule = validationRules[fieldName].find(
          rule => rule.type === 'maxLength'
        );
        if (maxLengthRule) {
          updateCharacterCount(fieldName, maxLengthRule.value);
        }
      });

      // ローカルストレージからフォームデータを削除
      localStorage.removeItem('contact_form_data');

      // 送信成功後にサンクスページへ遷移（通知表示のため短い遅延）
      setTimeout(() => {
        // Astro の base '/lp/' を考慮した遷移
        const base = '/lp';
        window.location.href = `${base}/thanks`;
      }, 600);
    } else {
      // エラー時の処理
      let errorMessage = result.message || 'エラーが発生しました';
      let notificationType = 'error';

      // エラーの種類に応じてメッセージを調整
      if (result.error_type === 'validation') {
        errorMessage = result.message;
        notificationType = 'warning';

        // サーバーサイドのバリデーションエラーを表示
        if (result.errors && Array.isArray(result.errors)) {
          result.errors.forEach((error, index) => {
            setTimeout(() => {
              showNotification(`${index + 1}. ${error}`, 'warning', 4000);
            }, index * 500);
          });
        }
      } else if (result.error_type === 'mail_send') {
        // メール送信エラーの場合、詳細情報を含める
        if (result.details && result.details.contact_info) {
          errorMessage += `<br><br>📞 ${result.details.contact_info}`;
        }
      } else if (result.error_type === 'system_error') {
        // システムエラーの場合、連絡先情報を表示
        if (result.details) {
          errorMessage += `<br><br>📞 ${result.details.contact_info || ''}`;
          if (result.details.email_info) {
            errorMessage += `<br>📧 ${result.details.email_info}`;
          }
        }
      }

      showNotification(errorMessage, notificationType, 10000);

      // フォームの先頭にスクロール（エラー確認のため）
      form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  } catch (error) {
    console.error('Form submission error:', error);

    let errorMessage = 'ネットワークエラーが発生しました。';

    if (error.name === 'TypeError') {
      errorMessage +=
        '接続に失敗しました。インターネット接続を確認してください。';
    } else if (error.name === 'SyntaxError') {
      errorMessage += 'サーバーからの応答が正しくありません。';
    } else {
      errorMessage += '時間をおいて再度お試しください。';
    }

    errorMessage +=
      '<br><br>問題が続く場合は、お電話にてお問い合わせください。<br>📞 03-5201-3756 (平日 9:00-18:00)';

    showNotification(errorMessage, 'error', 12000);
  } finally {
    // 送信ボタンを元に戻す
    submitButton.disabled = false;
    submitButton.classList.remove('opacity-75', 'cursor-not-allowed');
    submitButton.innerHTML = originalText;
  }

  return false;
}

// 初期化
document.addEventListener('DOMContentLoaded', function () {
  setupRealtimeValidation();

  const form = document.getElementById('contact-form');
  if (form) {
    form.addEventListener('submit', function (event) {
      event.preventDefault();
      handleFormSubmit(form);
    });
  }
});

// エクスポート（モジュールとして使用する場合）
if (typeof module !== 'undefined' && module.exports) {
  module.exports = {
    validateField,
    validateForm,
    showFieldError,
    removeFieldError,
    clearAllErrors,
    showNotification,
    handleFormSubmit,
  };
}
