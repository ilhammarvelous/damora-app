import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';

export function IconButton({ label, icon: Icon, variant = 'ghost', iconClassName, className, ...props }) {
    return (
        <Tooltip>
            <TooltipTrigger asChild>
                <Button
                    type="button"
                    variant={variant}
                    size="icon"
                    aria-label={label}
                    className={cn('size-9', className)}
                    {...props}
                >
                    {/* Ukuran ikon di sini, bukan di Button, agar tidak ditimpa aturan bawaan shadcn */}
                    <Icon className={cn('size-[18px]', iconClassName)} />
                </Button>
            </TooltipTrigger>
            <TooltipContent>{label}</TooltipContent>
        </Tooltip>
    );
}
