<template>
  <TableCustom
    @Items="getItems"
    :tableFields="columnActive.fields"
    :tableItems="saleParams.sale_agreement_item"
    :showFooter="true"
    :rowClass="tbodyTrClass"
    @rowClicked="addDiscountToLine"
  >
    <template slot="tgor_table:service_type" slot-scope="data">
      {{ data.item.booking_line_item.booking_type.reference_value_text }}
    </template>
    <template slot="tgor_table:niches" slot-scope="data">
      {{
        data.item.booking_line_item.booking_type.reference_value_text ==
        "Niches"
          ? data.item.booking_line_item.niche.reference_no
          : "--"
      }}
    </template>
    <template slot="tgor_table:rooms" slot-scope="data">
      {{
        data.item.booking_line_item.booking_type.reference_value_text ==
        "Memorial Rooms"
          ? data.item.booking_line_item.room.room_no
          : "--"
      }}
    </template>
    <template slot="tgor_table:remarks" slot-scope="data">
      {{ data.item.booking_line_item.remarks || "--" }}
    </template>
    <template slot="tgor_table:booking_line_item.amount" slot-scope="data">
      {{
        data.item.booking_line_item.amount &&
        data.item.booking_line_item.amount != ""
          ? +unFormatter(data.item.booking_line_item.amount)
          : 0 | formatMoney
      }}
    </template>
    <template slot="tgor_table:booking_line_item.tax_amount" slot-scope="data">
      {{
        data.item.booking_line_item.tax_amount &&
        data.item.booking_line_item.tax_amount != ""
          ? +unFormatter(data.item.booking_line_item.tax_amount)
          : 0 | formatMoney
      }}
      {{ gst ? `(${gst}%)` : "0%" }}
    </template>
    <template slot="tgor_table:booking_line_item.discount" slot-scope="data">
      <!-- data.item.booking_line_item.booking_type.reference_value_text == "Niches" -->
      <template>
        <template
          v-if="
            data.item.booking_line_item.discount_amount &&
              data.item.booking_line_item.discount_amount != ''
          "
        >
          {{
            +unFormatter(data.item.booking_line_item.discount_amount)
              | formatMoney
          }}
        </template>
        <template v-else>
          <span class="add-discount-custom">Add</span>
        </template>
      </template>

      <!-- {{ data.item.booking_line_item.discount && data.item.booking_line_item.discount != '' ? +unFormatter(data.item.booking_line_item.discount) : 0 | formatMoney }} -->
    </template>
    <template
      slot="tgor_table:booking_line_item.total_amount"
      slot-scope="data"
    >
      {{
        data.item.booking_line_item.total_amount
          ? +unFormatter(data.item.booking_line_item.total_amount)
          : 0 | formatMoney
      }}
    </template>
  </TableCustom>
</template>
<script>
import TableCustom from "../../components/Table/saleAgreementTable.vue";
import { SaleAgreementNiche } from "@/enums/saleAgreementTableHeader"
export default {
  props: {

  },
  components: {
    TableCustom,
    SaleAgreementNiche
  },
  data() {
    return {
      columnActive: SaleAgreementNiche
    }
  },
};
</script>
