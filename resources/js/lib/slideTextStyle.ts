import type { CSSProperties } from 'vue';
import type { SlideTextStyle } from '@/types/slide';

export function slideTextStyle(
    value: SlideTextStyle | null | undefined,
    kind: 'title' | 'description',
): CSSProperties {
    const hasTop =
        value?.top !== null && value?.top !== undefined && value.top !== '';
    const hasBottom =
        value?.bottom !== null &&
        value?.bottom !== undefined &&
        value.bottom !== '';
    const hasLeft =
        value?.left !== null && value?.left !== undefined && value.left !== '';
    const hasRight =
        value?.right !== null &&
        value?.right !== undefined &&
        value.right !== '';
    const size = Number(value?.size || (kind === 'title' ? 48 : 20));

    return {
        top: hasTop
            ? `${value.top}%`
            : hasBottom
              ? undefined
              : kind === 'title'
                ? '22%'
                : '43%',
        bottom: hasBottom ? `${value.bottom}%` : undefined,
        left: hasLeft ? `${value.left}%` : hasRight ? undefined : '12%',
        right: hasRight ? `${value.right}%` : undefined,
        fontSize: `clamp(12px, ${size / 9}cqw, ${size}px)`,
        fontWeight: Number(value?.weight || (kind === 'title' ? 700 : 400)),
        color: value?.color || '#ffffff',
    };
}
