import { Decimal } from 'decimal.js'

export function plus(a, b) {
  const _a = new Decimal(a)
  const _b = new Decimal(b)
  return _a.add(_b).toNumber()
}

export function minus(a, b) {
  const _a = new Decimal(a)
  const _b = new Decimal(b)
  return _a.sub(_b).toNumber()
}

export function multiply(a, b) {
  const _a = new Decimal(a)
  const _b = new Decimal(b)
  return _a.mul(_b).toNumber()
}

export function divide(a, b) {
  const _a = new Decimal(a)
  const _b = new Decimal(b)
  return _a.div(_b).toNumber()
}
