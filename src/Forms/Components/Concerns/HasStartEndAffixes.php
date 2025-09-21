<?php

namespace CodeWithKyrian\FilamentDateRange\Forms\Components\Concerns;

use Closure;
use Illuminate\Support\Arr;
use Filament\Actions\Action;
use Filament\Support\Enums\Size;
use Illuminate\Contracts\Support\Htmlable;

trait HasStartEndAffixes
{
    /**
     * @var array<Action> | null
     */
    protected ?array $cachedStartPrefixActions = null;

    /**
     * @var array<Action | Closure>
     */
    protected array $startPrefixActions = [];

    protected string | Htmlable | Closure | null $startPrefixLabel = null;

    protected string | Closure | null $startPrefixIcon = null;

    /**
     * @var string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null
     */
    protected string | array | Closure | null $startPrefixIconColor = null;

    protected bool | Closure $isStartPrefixInline = false;

    protected ?array $cachedStartSuffixActions = null;

    protected array $startSuffixActions = [];

    protected string | Htmlable | Closure | null $startSuffixLabel = null;

    protected string | Closure | null $startSuffixIcon = null;

    /**
     * @var string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null
     */
    protected string | array | Closure | null $startSuffixIconColor = null;

    protected bool | Closure $isStartSuffixInline = true;

    /**
     * @var array<Action> | null
     */
    protected ?array $cachedEndPrefixActions = null;

    /**
     * @var array<Action | Closure>
     */
    protected array $endPrefixActions = [];

    protected string | Htmlable | Closure | null $endPrefixLabel = null;

    protected string | Closure | null $endPrefixIcon = null;

    /**
     * @var string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null
     */
    protected string | array | Closure | null $endPrefixIconColor = null;

    protected bool | Closure $isEndPrefixInline = false;

    protected ?array $cachedEndSuffixActions = null;

    protected array $endSuffixActions = [];

    protected string | Htmlable | Closure | null $endSuffixLabel = null;

    protected string | Closure | null $endSuffixIcon = null;

    /**
     * @var string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null
     */
    protected string | array | Closure | null $endSuffixIconColor = null;

    protected bool | Closure $isEndSuffixInline = true;

    public function startPrefix(string | Htmlable | Closure | null $label, bool | Closure $isInline = false): static
    {
        $this->startPrefixLabel = $label;
        $this->inlineStartPrefix($isInline);
        return $this;
    }

    public function startPrefixAction(Action | Closure $action, bool | Closure $isInline = false): static
    {
        $this->startPrefixActions([$action], $isInline);
        return $this;
    }

    /** @param  array<Action | Closure>  $actions */
    public function startPrefixActions(array $actions, bool | Closure $isInline = false): static
    {
        $this->startPrefixActions = [...$this->startPrefixActions, ...$actions];
        $this->inlineStartPrefix($isInline);
        return $this;
    }

    public function startSuffix(string | Htmlable | Closure | null $label, bool | Closure $isInline = true): static
    {
        $this->startSuffixLabel = $label;
        $this->inlineStartSuffix($isInline);
        return $this;
    }

    public function startSuffixAction(Action | Closure $action, bool | Closure $isInline = true): static
    {
        $this->startSuffixActions([$action], $isInline);
        return $this;
    }

    /** @param  array<Action | Closure>  $actions */
    public function startSuffixActions(array $actions, bool | Closure $isInline = true): static
    {
        $this->startSuffixActions = [...$this->startSuffixActions, ...$actions];
        $this->inlineStartSuffix($isInline);
        return $this;
    }

    public function inlineStartPrefix(bool | Closure $isInline = true): static
    {
        $this->isStartPrefixInline = $isInline;
        return $this;
    }

    public function inlineStartSuffix(bool | Closure $isInline = true): static
    {
        $this->isStartSuffixInline = $isInline;
        return $this;
    }

    public function startPrefixIcon(string | Closure | null $icon, bool | Closure $isInline = false): static
    {
        $this->startPrefixIcon = $icon;
        $this->inlineStartPrefix($isInline);
        return $this;
    }

    public function startPrefixIconColor(string | array | Closure | null $color = null): static
    {
        $this->startPrefixIconColor = $color;
        return $this;
    }

    public function startSuffixIcon(string | Closure | null $icon, bool | Closure $isInline = true): static
    {
        $this->startSuffixIcon = $icon;
        $this->inlineStartSuffix($isInline);
        return $this;
    }

    public function startSuffixIconColor(string | array | Closure | null $color = null): static
    {
        $this->startSuffixIconColor = $color;
        return $this;
    }

    public function endPrefix(string | Htmlable | Closure | null $label, bool | Closure $isInline = false): static
    {
        $this->endPrefixLabel = $label;
        $this->inlineEndPrefix($isInline);
        return $this;
    }

    public function endPrefixAction(Action | Closure $action, bool | Closure $isInline = false): static
    {
        $this->endPrefixActions([$action], $isInline);
        return $this;
    }

    /** @param  array<Action | Closure>  $actions */
    public function endPrefixActions(array $actions, bool | Closure $isInline = false): static
    {
        $this->endPrefixActions = [...$this->endPrefixActions, ...$actions];
        $this->inlineEndPrefix($isInline);
        return $this;
    }

    public function endSuffix(string | Htmlable | Closure | null $label, bool | Closure $isInline = true): static
    {
        $this->endSuffixLabel = $label;
        $this->inlineEndSuffix($isInline);
        return $this;
    }

    public function endSuffixAction(Action | Closure $action, bool | Closure $isInline = true): static
    {
        $this->endSuffixActions([$action], $isInline);
        return $this;
    }

    /** @param  array<Action | Closure>  $actions */
    public function endSuffixActions(array $actions, bool | Closure $isInline = true): static
    {
        $this->endSuffixActions = [...$this->endSuffixActions, ...$actions];
        $this->inlineEndSuffix($isInline);
        return $this;
    }

    public function inlineEndPrefix(bool | Closure $isInline = true): static
    {
        $this->isEndPrefixInline = $isInline;
        return $this;
    }

    public function inlineEndSuffix(bool | Closure $isInline = true): static
    {
        $this->isEndSuffixInline = $isInline;
        return $this;
    }

    public function endPrefixIcon(string | Closure | null $icon, bool | Closure $isInline = false): static
    {
        $this->endPrefixIcon = $icon;
        $this->inlineEndPrefix($isInline);
        return $this;
    }

    public function endPrefixIconColor(string | array | Closure | null $color = null): static
    {
        $this->endPrefixIconColor = $color;
        return $this;
    }

    public function endSuffixIcon(string | Closure | null $icon, bool | Closure $isInline = true): static
    {
        $this->endSuffixIcon = $icon;
        $this->inlineEndSuffix($isInline);
        return $this;
    }

    public function endSuffixIconColor(string | array | Closure | null $color = null): static
    {
        $this->endSuffixIconColor = $color;
        return $this;
    }

    /** @return array<Action> */
    public function getStartPrefixActions(): array
    {
        return $this->cachedStartPrefixActions ?? $this->cacheStartPrefixActions();
    }

    /** @return array<Action> */
    public function cacheStartPrefixActions(): array
    {
        $this->cachedStartPrefixActions = [];
        foreach ($this->startPrefixActions as $action) {
            foreach (Arr::wrap($this->evaluate($action)) as $evaluatedAction) {
                $this->cachedStartPrefixActions[$evaluatedAction->getName()] = $this->prepareAction(
                    $evaluatedAction->defaultSize(Size::Small)->defaultView(Action::ICON_BUTTON_VIEW)
                );
            }
        }
        return $this->cachedStartPrefixActions;
    }

    /** @return array<Action> */
    public function getStartSuffixActions(): array
    {
        return $this->cachedStartSuffixActions ?? $this->cacheStartSuffixActions();
    }

    /** @return array<Action> */
    public function cacheStartSuffixActions(): array
    {
        $this->cachedStartSuffixActions = [];
        foreach ($this->startSuffixActions as $action) {
            foreach (Arr::wrap($this->evaluate($action)) as $evaluatedAction) {
                $this->cachedStartSuffixActions[$evaluatedAction->getName()] = $this->prepareAction(
                    $evaluatedAction->defaultSize(Size::Small)->defaultView(Action::ICON_BUTTON_VIEW)
                );
            }
        }
        return $this->cachedStartSuffixActions;
    }

    public function getStartPrefixLabel(): string | Htmlable | null
    {
        return $this->evaluate($this->startPrefixLabel);
    }

    public function getStartSuffixLabel(): string | Htmlable | null
    {
        return $this->evaluate($this->startSuffixLabel);
    }

    public function getStartPrefixIcon(): ?string
    {
        return $this->evaluate($this->startPrefixIcon);
    }

    public function getStartPrefixIconColor(): string | array | null
    {
        return $this->evaluate($this->startPrefixIconColor);
    }

    public function getStartSuffixIcon(): ?string
    {
        return $this->evaluate($this->startSuffixIcon);
    }

    public function getStartSuffixIconColor(): string | array | null
    {
        return $this->evaluate($this->startSuffixIconColor);
    }

    public function isStartPrefixInline(): bool
    {
        return (bool) $this->evaluate($this->isStartPrefixInline);
    }

    public function isStartSuffixInline(): bool
    {
        return (bool) $this->evaluate($this->isStartSuffixInline);
    }

    /** @return array<Action> */
    public function getEndPrefixActions(): array
    {
        return $this->cachedEndPrefixActions ?? $this->cacheEndPrefixActions();
    }

    /** @return array<Action> */
    public function cacheEndPrefixActions(): array
    {
        $this->cachedEndPrefixActions = [];
        foreach ($this->endPrefixActions as $action) {
            foreach (Arr::wrap($this->evaluate($action)) as $evaluatedAction) {
                $this->cachedEndPrefixActions[$evaluatedAction->getName()] = $this->prepareAction(
                    $evaluatedAction->defaultSize(Size::Small)->defaultView(Action::ICON_BUTTON_VIEW)
                );
            }
        }
        return $this->cachedEndPrefixActions;
    }

    /** @return array<Action> */
    public function getEndSuffixActions(): array
    {
        return $this->cachedEndSuffixActions ?? $this->cacheEndSuffixActions();
    }

    /** @return array<Action> */
    public function cacheEndSuffixActions(): array
    {
        $this->cachedEndSuffixActions = [];
        foreach ($this->endSuffixActions as $action) {
            foreach (Arr::wrap($this->evaluate($action)) as $evaluatedAction) {
                $this->cachedEndSuffixActions[$evaluatedAction->getName()] = $this->prepareAction(
                    $evaluatedAction->defaultSize(Size::Small)->defaultView(Action::ICON_BUTTON_VIEW)
                );
            }
        }
        return $this->cachedEndSuffixActions;
    }

    public function getEndPrefixLabel(): string | Htmlable | null
    {
        return $this->evaluate($this->endPrefixLabel);
    }

    public function getEndSuffixLabel(): string | Htmlable | null
    {
        return $this->evaluate($this->endSuffixLabel);
    }

    public function getEndPrefixIcon(): ?string
    {
        return $this->evaluate($this->endPrefixIcon);
    }

    public function getEndPrefixIconColor(): string | array | null
    {
        return $this->evaluate($this->endPrefixIconColor);
    }

    public function getEndSuffixIcon(): ?string
    {
        return $this->evaluate($this->endSuffixIcon);
    }

    public function getEndSuffixIconColor(): string | array | null
    {
        return $this->evaluate($this->endSuffixIconColor);
    }

    public function isEndPrefixInline(): bool
    {
        return (bool) $this->evaluate($this->isEndPrefixInline);
    }

    public function isEndSuffixInline(): bool
    {
        return (bool) $this->evaluate($this->isEndSuffixInline);
    }
}
