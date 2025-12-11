import i18n from '@/lang/index.js'

//  系统默认语言
export const default_lang = {
  language_name: 'English',
  chinese_name: '英文',
  file_name: 'en'
}

export const order_statuses = [
  { name: i18n.global.t('all'), value: 'all' },
  { name: i18n.global.t('waitingPay'), value: '0' },
  { name: i18n.global.t('waitingDelivier'), value: '1' },
  { name: i18n.global.t('deliveried'), value: '2' },
  { name: i18n.global.t('received'), value: '3' },
  { name: i18n.global.t('completed'), value: '4' },
  { name: i18n.global.t('cancelled'), value: '-1' },
  //  虚拟状态
  { name: i18n.global.t('inAfterSale'), value: '-4' }
]

//  提现,充值记录状态
export const fund_record_statuses = [
  { name: i18n.global.t('all'), value: 'all' },
  { name: i18n.global.t('refuese'), value: '2' },
  { name: i18n.global.t('auditing'), value: '0' },
  { name: i18n.global.t('passed'), value: '1' }
]
