<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_profile_id',
        'category_id',
        'unit',
        'units_per_package',
        'sku',
        'moq',
        'base_price',
        'currency',
        'status',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'units_per_package' => 'integer',
        ];
    }

    public function isPackagingUnit(): bool
    {
        $unit = strtolower(trim($this->unit ?? ''));
        return in_array($unit, ['carton', 'box', 'set'], true);
    }

    public function getPackageDetailsAttribute(): ?string
    {
        if (!$this->isPackagingUnit() || empty($this->units_per_package)) {
            return null;
        }

        return __('products.package_pieces_format', [
            'count' => $this->units_per_package,
            'unit' => $this->localized_unit,
        ]);
    }

    public function businessProfile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function specifications(): HasMany
    {
        return $this->hasMany(ProductSpecification::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function getCoverImageAttribute()
    {
        $primary = $this->images->where('is_primary', true)->first();
        return $primary ? $primary->file_url : ($this->images->first()?->file_url ?? null);
    }

    public function getNameAttribute($value)
    {
        $locale = app()->getLocale();
        $translation = $this->translations->where('locale', $locale)->first()
            ?? $this->translations->where('locale', 'en')->first()
            ?? $this->translations->first();
        return $translation ? $translation->name : null;
    }

    public function getShortDescriptionAttribute($value)
    {
        $locale = app()->getLocale();
        $translation = $this->translations->where('locale', $locale)->first()
            ?? $this->translations->where('locale', 'en')->first()
            ?? $this->translations->first();
        return $translation ? $translation->short_description : null;
    }

    public function getFullDescriptionAttribute($value)
    {
        $locale = app()->getLocale();
        $translation = $this->translations->where('locale', $locale)->first()
            ?? $this->translations->where('locale', 'en')->first()
            ?? $this->translations->first();
        return $translation ? $translation->full_description : null;
    }

    public function getLocalizedUnitAttribute()
    {
        if (!$this->unit) {
            return __('products.unit_piece');
        }

        $cleanUnit = mb_strtolower(trim($this->unit));

        // Canonical mapping for standard units entered in any supported language
        $unitAliases = [
            'piece' => ['piece', 'pieces', 'pc', 'pcs', 'قطعة', 'قطعه', 'قطع', 'حبة', 'حبه', 'pieza', 'piezas', 'stk', 'stück', 'stueck', 'adet', 'tane', '件', '个', '個'],
            'kg' => ['kg', 'kilogram', 'kilograms', 'كغ', 'كيلو', 'كيلوجرام', 'كجم', 'kilo', 'quilogramo', 'kilogramm', '公斤', '千克'],
            'carton' => ['carton', 'cartons', 'ctn', 'كرتون', 'كرتونة', 'كرتونه', 'cartón', 'karton', 'koli', '箱'],
            'box' => ['box', 'boxes', 'علبة', 'علبه', 'صندوق', 'caja', 'cajas', 'schachtel', 'kutu', '盒'],
            'meter' => ['meter', 'meters', 'm', 'متر', 'أمتار', 'امتار', 'metro', 'metros', 'metre', '米'],
            'set' => ['set', 'sets', 'طقم', 'أطقم', 'اطقم', 'مجموعة', 'juego', 'takım', 'takim', '套'],
            'ton' => ['ton', 'tons', 't', 'طن', 'أطنان', 'اطنان', 'tonelada', 'tonne', '吨'],
            'liter' => ['liter', 'liters', 'l', 'لتر', 'ليتر', 'litro', 'litre', '升'],
        ];

        foreach ($unitAliases as $canonical => $aliases) {
            if ($cleanUnit === $canonical || in_array($cleanUnit, $aliases, true)) {
                return __('products.unit_' . $canonical);
            }
        }

        $translationKey = 'products.unit_' . $cleanUnit;
        if (\Illuminate\Support\Facades\Lang::has($translationKey)) {
            return __($translationKey);
        }

        return $this->unit;
    }

    public function getCurrencySymbolAttribute(): string
    {
        $currency = $this->currency ?: 'EGP';
        if ($currency === 'USD') {
            return '$';
        }

        return app()->getLocale() === 'ar' ? 'ج.م' : 'EGP';
    }

    public function getFormattedPriceAttribute(): ?string
    {
        if ($this->base_price === null) {
            return null;
        }

        $formatted = number_format($this->base_price, 2);
        $currency = $this->currency ?: 'EGP';

        if ($currency === 'USD') {
            return '$' . $formatted;
        }

        $symbol = app()->getLocale() === 'ar' ? 'ج.م' : 'EGP';
        return $formatted . ' ' . $symbol;
    }
}
