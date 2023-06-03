<?php

namespace Database\Seeders;

use App\Models\CollectionWithProduct;
use App\Models\SubscriptionEmailFormat;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolePermissionSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(PaymentSeeder::class);
        $this->call(ProductCollectionSeeder::class);
        $this->call(TaxRuleSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(PageSeeder::class);
        $this->call(BundleDealSeeder::class);
        $this->call(FooterImageLinkSeeder::class);
        $this->call(FooterLinkSeeder::class);
        $this->call(ShippingRulesSeeder::class);
        $this->call(ShippingPlaceSeeder::class);
        $this->call(BrandSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(SubCategorySeeder::class);
        $this->call(HomeSliderSeeder::class);
        $this->call(BannerSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(CollectionWithProductSeeder::class);
        $this->call(AttributeSeeder::class);
        $this->call(AttributeValueSeeder::class);
        $this->call(VoucherSeeder::class);
        $this->call(FlashSaleSeeder::class);
        $this->call(FlashSaleProductSeeder::class);
        $this->call(ProductImageSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(UserAddressSeeder::class);
        $this->call(UpdatedInventorySeeder::class);
        $this->call(InventoryAttributeSeeder::class);
        $this->call(OrderSeeder::class);
        $this->call(OrderedProductSeeder::class);
        $this->call(RatingReviewSeeder::class);
        $this->call(ReviewImageSeeder::class);
        $this->call(WithdrawalAccountSeeder::class);
        $this->call(WithdrawalSeeder::class);
        $this->call(SubscriptionEmailFormatSeeder::class);
        $this->call(SiteSettingSeeder::class);
        $this->call(StoreSeeder::class);
    }
}
