export const ReceiptNiche = {
  fields: [
    {
      key: " ",
      label: "",
      thClass: "checkbox-column text-center",
      tdClass: "checkbox-column text-center",
      thStyle: "width: 10px",
      isActive: 1,
    },
    {
      key: "service_type",
      label: "Service Type",
      isActive: 1,
      keySearch: "id",
      type: "text",
      isFilter: true,
      thStyle: "width: 150px",
    },
    {
      key: "location",
      label: `Location`,
      thStyle: "width: 250px",
      isActive: 1,
      isFilter: true,
    },
    {
      key: "detail",
      label: `Details`,
      thStyle: "width: 150px",
      isActive: 1,
      isFilter: true,
    },
    {
      key: "niches",
      label: "Niche Ref",
      thStyle: "width: 150px",
      isActive: 1,
      isFilter: true,
    },
    {
      key: "lease",
      label: "Lease",
      thStyle: "width: 250px",
      isActive: 1,
      isFilter: true,
    },
    {
      key: "remarks",
      label: `Remarks`,
      isActive: 1,
      thStyle: "width: 500px",
      isFilter: true,
    },
    {
      key: "booking_line_item.amount",
      label: "Amount",
      isActive: 1,
      thStyle: "width: 100px",
      isFilter: true,
    },
    {
      key: "booking_line_item.discount",
      label: "Discount",
      isActive: 1,
      thStyle: "width: 100px",
      isFilter: true,
    },
    {
      key: "booking_line_item.tax_amount",
      label: "GST",
      isActive: 1,
      thStyle: "width: 100px",
      isFilter: true,
    },
    {
      key: "booking_line_item.total_amount",
      label: "Total",
      isActive: 1,
      thStyle: "width: 100px",
      isFilter: true,
    },
  ],
  show: [],
  hide: [],
}
export const ReceiptRoom = {
  fields: [
    {
      key: " ",
      label: "",
      thClass: "checkbox-column text-center",
      tdClass: "checkbox-column text-center",
      thStyle: "width: 10px",
      isActive: 1,
    },
    {
      key: "invoice_no",
      label: "Invoice #",
      isActive: 1,
      type: "text",
      isFilter: true,
      thStyle: "width: 150px",
    },
    {
      key: "service_type",
      label: "Service Type",
      isActive: 1,
      keySearch: "id",
      type: "text",
      isFilter: true,
      thStyle: "width: 150px",
    },
    {
      key: "facility",
      label: `Facility`,
      thStyle: "width: 150px",
      isActive: 1,
      isFilter: true,
    },
    {
      key: "event",
      label: `Event`,
      thStyle: "width: 150px",
      isActive: 1,
      isFilter: true,
    },
    {
      key: "rate_type",
      label: "Rate Type",
      thStyle: "width: 150px",
      isActive: 1,
      isFilter: true,
    },
    {
      key: "rate",
      label: "Rate",
      thStyle: "width: 250px",
      isActive: 1,
      isFilter: true,
    },
    {
      key: "period",
      label: "Period",
      thStyle: "width: 250px",
      isActive: 1,
      isFilter: true,
    },
    {
      key: "day",
      label: "No. of Day(s)",
      thStyle: "width: 150px",
      isActive: 1,
      isFilter: true,
    },
    {
      key: "remarks",
      label: `Remarks`,
      isActive: 1,
      thStyle: "width: 450px",
      isFilter: true,
    },
    {
      key: "booking_line_item.amount",
      label: "Amount",
      isActive: 1,
      thStyle: "width: 100px",
      isFilter: true,
    },
    {
      key: "booking_line_item.discount",
      label: "Discount",
      isActive: 1,
      thStyle: "width: 100px",
      isFilter: true,
    },
    {
      key: "booking_line_item.tax_amount",
      label: "GST",
      isActive: 1,
      thStyle: "width: 100px",
      isFilter: true,
    },
    {
      key: "booking_line_item.total_amount",
      label: "Total",
      isActive: 1,
      thStyle: "width: 100px",
      isFilter: true,
    },
  ],
  show: [],
  hide: [],
}
export const ReceiptOther = {
  fields: [
    {
      key: " ",
      label: "",
      thClass: "checkbox-column text-center",
      tdClass: "checkbox-column text-center",
      thStyle: "width: 10px",
      isActive: 1,
    },
    {
      key: "invoice_no",
      label: "Invoice #",
      isActive: 1,
      type: "text",
      isFilter: true,
      thStyle: "width: 250px",
    },
    {
      key: "service_type",
      label: "Service Type",
      isActive: 1,
      keySearch: "id",
      type: "text",
      isFilter: true,
      thStyle: "width: 250px",
    },
    {
      key: "remarks",
      label: `Remarks`,
      isActive: 1,
      thStyle: "width: 550px",
      isFilter: true,
    },
    {
      key: "booking_line_item.amount",
      label: "Amount",
      isActive: 1,
      thStyle: "width: 200px",
      isFilter: true,
    },
    {
      key: "booking_line_item.discount",
      label: "Discount",
      isActive: 1,
      thStyle: "width: 200px",
      isFilter: true,
    },
    {
      key: "booking_line_item.tax_amount",
      label: "GST",
      isActive: 1,
      thStyle: "width: 200px",
      isFilter: true,
    },
    {
      key: "booking_line_item.total_amount",
      label: "Total",
      isActive: 1,
      thStyle: "width: 200px",
      isFilter: true,
    },
  ],
  show: [],
  hide: [],
}