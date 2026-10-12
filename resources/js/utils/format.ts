export function onlyDigits(value: string): string {
  return (value || '').replace(/\D/g, '');
}

export function formatCnpj(value: string): string {
  const d = onlyDigits(value).slice(0, 14);

  if (d.length <= 2) return d;
  if (d.length <= 5) return `${d.slice(0, 2)}.${d.slice(2)}`;
  if (d.length <= 8) return `${d.slice(0, 2)}.${d.slice(2, 5)}.${d.slice(5)}`;
  if (d.length <= 12) {
    return `${d.slice(0, 2)}.${d.slice(2, 5)}.${d.slice(5, 8)}/${d.slice(8)}`;
  }

  return `${d.slice(0, 2)}.${d.slice(2, 5)}.${d.slice(5, 8)}/${d.slice(8, 12)}-${d.slice(12, 14)}`;
}

export function formatMoney(value: number | string | null | undefined): string {
  const amount = Number(value ?? 0);
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(amount);
}

/** Digitação de dinheiro: "1234" → "12,34"  /  "123456" → "1.234,56" */
export function formatMoneyInput(value: string): string {
  const digits = onlyDigits(value);
  if (!digits) return '';

  const number = Number(digits) / 100;
  return number.toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
}

export function parseMoneyInput(value: string): number {
  const digits = onlyDigits(value);
  if (!digits) return 0;
  return Number(digits) / 100;
}

export const biddingStatuses: Record<string, string> = {
  draft: 'Rascunho',
  open: 'Aberto',
  awarded: 'Homologado',
  cancelled: 'Cancelado',
};

export const contractStatuses: Record<string, string> = {
  active: 'Ativo',
  closed: 'Encerrado',
  cancelled: 'Cancelado',
};